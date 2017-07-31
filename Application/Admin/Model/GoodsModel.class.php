<?php
namespace Admin\Model;

use Think\Model;
class GoodsModel extends Model
{
    //添加时允许接收的字段，这样可以防止前端输入一些错误的字段而导致错误，比如伪造ID等等
    protected  $insertFields = 'goods_name,market_price,shop_price,is_on_sale,goods_desc,brand_id,cat_id';
    //修改时允许接收的字段
    protected  $updateFields = 'id,goods_name,market_price,shop_price,is_on_sale,goods_desc,brand_id,cat_id';
   //定义验证规则
	protected $_validate = array(
		array('goods_name', 'require', '商品名称不能为空！', 1),
		array('market_price', 'currency', '市场价格必须是货币类型！', 1),
		array('shop_price', 'currency', '本店价格必须是货币类型！', 1),
        array('cat_id', 'require', '商品必须选择主分类！', 1),
	);

    /**
     * 钩子函数，添加之前的调用函数
     * @param $data,即将插入到数据库中的数据
     * @param $option
     * &按引用传递，内部函数要修改外部函数的变量必须使用引用函数，除非修改的是对象
     */
    protected function _before_insert(&$data,$option){
        //*****************处理logo的代码*********************
        //判断有没有传图片
        if($_FILES['logo']['error'] == 0){
            $ret = uploadOne('logo','Goods',array(
                array(700,700),
                array(350,350),
                array(130,130),
                array(30,30)
            ));

            $data['logo'] = $ret['images'][0];
            $data['mbig_logo'] = $ret['images'][1];
            $data['big_logo'] = $ret['images'][2];
            $data['mid_logo'] = $ret['images'][3];
            $data['sm_logo'] = $ret['images'][4];
        }
        //获取当前时间
       $data['addtime'] = date('Y-m-d H:i:s',time());
        //使用自定义的过滤XSS方法过滤商品描述
        $data['goods_desc'] = removeXSS($_POST['goods_desc']);

    }
    /**
     * 钩子函数，修改之前的调用函数
     * @param $data,即将插入到数据库中的数据
     * @param $option
     * &按引用传递，内部函数要修改外部函数的变量必须使用引用函数，除非修改的是对象
     */
    protected function _before_update(&$data,$option){
        $id = $option['where']['id'];
        //*****************处理logo的代码*********************
        //判断有没有传图片
        if($_FILES['logo']['error'] == 0){
            //上传图片
          $ret = uploadOne('logo','Goods',array(
              array(700,700),
              array(350,350),
              array(150,150),
              array(50,50),
          ));
            $data['logo'] = $ret['images'][0];
            $data['mbig_logo'] = $ret['images'][1];
            $data['big_logo'] = $ret['images'][2];
            $data['mid_logo'] = $ret['images'][3];
            $data['sm_logo'] = $ret['images'][4];
            //**************删除图片********************
            $oldLogo = $this->field('logo,mbig_logo,big_logo,mid_logo,sm_logo')->find($id);
            deleteImage($oldLogo);


        }
        //使用自定义的过滤XSS方法过滤商品描述
        $data['goods_desc'] = removeXSS($_POST['goods_desc']);

    }
    protected function _before_delete($option){
        $id = $option['where']['id'];//要删除的商品的id
        //***********删除原图*****************
        $oldLogo = $this->field('logo,sm_logo,mid_logo,big_logo,mbig_logo')->find($id);
        //从硬盘中删除
       deleteImage($oldLogo);

    }
    /*
     * 商品添加之后会调用这个方法，其中$data['id']就是添加的商品的ID
     */
    protected function _after_insert($data,$option){
        $id = $data['id'];
        //************处理扩展分类**********
        $ecid = I('post.ext_cat_id');
        if($ecid){
            $gcModel = D('GoodsCat');
            foreach($ecid as $k =>$v){
               if(empty($v))
                   continue;
                $gcModel->add(array(
                    'cat_id'=>$v,
                    'goods_id'=>$data['id']
                ));

            }
        }

        //************处理会员价格***********
        $mp = I('post.member_price');
        $mpModle = D('MemberPrice');
        foreach($mp as $k =>$v){
            //如果设置了价格就插入表
            $_v = (float)$v;
            if($_v > 0){
                $mpModle->add(array(
                    'price'=>$_v,
                    'level_id'=>$k,
                    'goods_id'=>$id
                ));
            }


        }

    }
    /**
     * 实现翻页／搜索和排序
     */
    public function search($perPage = 5){
        $where = array();
        $gn = I('get.gn');
        if($gn){
            $where['a.goods_name'] = array('like',"%$gn%");
        }
        $fp = I('get.fp');
        $tp = I('get.tp');
        if($fp && $tp){
            $where['a.shop_price'] = array('between',array($fp,$tp));
        }elseif($tp){
            $where['a.shop_price'] = array('egt',$tp);
        }elseif($fp){
            $where['a.shop_price'] = array('elt',$tp);
        }
        $ios = I('get.ios');
        if(!empty($ios)){
            $where['a.is_on_sale'] = array('eq',$ios);
        }
        $fa = I('get.fa');
        $ta = I('get.ta');
        if($fa && $ta)
            $where['a.addtime'] = array('between', array($fa, $ta)); // WHERE shop_price BETWEEN $fp AND $tp
        elseif ($fa)
            $where['a.addtime'] = array('egt', $fa);   // WHERE shop_price >= $fp
        elseif ($ta)
            $where['a.addtime'] = array('elt', $ta);   // WHERE shop_price <= $fp
        //品牌
        $brandId = I('get.brand_id');
        if($brandId)
            $where['a.brand_id'] = array('eq',$brandId);
        //商品分类id
        $cat_id = I('get.cat_id');
        if($cat_id){
            //取出所有子分类的id
            $catModel = D('Category');
            $children = $catModel->getChildren($cat_id);
            $children[] = $cat_id;
            //搜索出所有这些分类下的商品
            $where['a.cat_id'] = array('IN',$children);

        }


        //**************翻页****************
        //取出总的记录数
        $count = $this->alias('a')->where($where)->count();
        //生成翻页类的对象
        $pageObj = new \Think\Page($count, $perPage);
        $pageObj->setConfig('next','下一页');
        $pageObj->setConfig('prev','上一页');
        //生成页面下面显示的上一页，下一页的字符串
        $pageString = $pageObj->show();

        //****************排序***********************
        $orderby = 'a.id';//默认的排序字段
        $orderway = 'desc';//默认的排序方式
        $odby = I('get.odby');
        if($odby){
            if($odby == 'id_asc'){
                $orderway = 'asc';
            }elseif($odby == 'price_desc'){
                $orderby = 'a.shop_price';
            }elseif($odby == 'price_asc'){
                $orderby = 'a.shop_price';
                $orderway = 'asc';
            }
        }
        //**************取某一页的数据*****************`
        $data = $this->order("$orderby $orderway")
            ->field('a.*,b.brand_name,c.cat_name,GROUP_CONCAT(e.cat_name SEPARATOR "<br />") ext_cat_name')
            ->alias('a')
            ->join('LEFT JOIN __BRAND__ b ON a.brand_id = b.id
                    LEFT JOIN __CATEGORY__ c ON a.cat_id = c.id
                    LEFT JOIN __GOODS_CAT__ d ON a.id = d.goods_id
                    LEFT JOIN __CATEGORY__ e ON d.cat_id = e.id
            ')
            ->where($where)
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->group('a.id')
            ->select();
        return array(
            'data'=>$data,//page
            'page'=>$pageString//翻页字符串
        );

    }
}

?>