<?php
namespace Admin\Controller;

use Think\Controller;
class GoodsController extends Controller
{
    /*
     * 显示和处理表单
     */
    public function add(){
        // 判断用户是否提交了表单
        if(IS_POST)
        {
            $model = D("Goods");
            // 2. CREATE方法：a. 接收数据并保存到模型中 b.根据模型中定义的规则验证表单
            /**
            * 第一个参数：要接收的数据默认是$_POST
            * 第二个参数：表单的类型。当前是添加还是修改的表单,1：添加 2：修改
            * $_POST：表单中原始的数据 ，I('post.')：过滤之后的$_POST的数据，过滤XSS攻击
            */
    
            if($model->create(I('post'),1))//1,代表添加，2代表更新
            {
            // 插入到数据库中
            if($model->add())  // 在add()里又先调用了_before_insert方法
            {
            // 显示成功信息并等待1秒之后跳转
            $this->success('操作成功！', U('lst'));
                exit;
            }
            }
            // 如果走到 这说明上面失败了在这里处理失败的请求
            // 从模型中取出失败的原因
            $error = $model->getError();
            // 由控制器显示错误信息,并在3秒跳回上一个页面
            $this->error($error);
        }
        //取出所有的品牌
//        $brandModel = D('Brand');
//        $brandData = $brandModel->select();
        //取出所有的会员级别
        $mlModel = D('MemberLevel');
        $mlData = $mlModel->select();
        //取出商品分类
        $catModel = D('Category');
        $catData = $catModel->getTree();
        //设置页面信息
        $this->assign(array(
            'catData'=>$catData,
            'mlData'=>$mlData,
            '_page_btn_link'=>U('lst'),
            '_page_title'=>'添加新商品',
            '_page_btn_name'=>'商品列表'
        ));
        // 1.显示表单
        $this->display();
        }

    /*
    * 修改表单
    */
    public function edit(){
        $id = I('get.id');//要修改的商品ID
        $model = D("Goods");
        // 判断用户是否提交了表单
        if(IS_POST)
        {

            if($model->create(I('post'),2))//1,代表添加，2代表更新
            {
                // 插入到数据库中
                if(FALSE !== $model->save())  //save()的返回值是，如果失败则返回0，如果成功返回受影响的行数，如果修改前和修改后相同则返回0
                {
                    // 显示成功信息并等待1秒之后跳转
                    $this->success('操作成功！', U('lst'));
                    exit;
                }
            }
            // 如果走到 这说明上面失败了在这里处理失败的请求
            // 从模型中取出失败的原因
            $error = $model->getError();
            // 由控制器显示错误信息,并在3秒跳回上一个页面
            $this->error($error);
        }
        $data = $model->find($id);
        $this->assign('data',$data);
        //取出所有的品牌
//        $brandModel = D('Brand');
//        $brandData = $brandModel->select();
        /// 取出所有的分类做下拉框
        $catModel = D('Category');
        $catData= $catModel->getTree();
        //设置页面信息
        $this->assign(array(
            'catData'=>$catData,
//            'brandData'=>$brandData,
            '_page_btn_link'=>U('lst'),
            '_page_title'=>'修改商品',
            '_page_btn_name'=>'商品列表'
        ));
        // 1.显示表单
        $this->display();
    }

    /**
     * 商品列表页
     */
        public function lst(){
            $model = D('Goods');
            //返回数据和翻页
            $data = $model->search();
            $this->assign($data);
            //取出商品分类
            $catModel = D('Category');
            $catData = $catModel->getTree();
            //设置页面信息
            $this->assign(array(
                'catData'=>$catData,
                '_page_btn_link'=>U('add'),
                '_page_title'=>'商品列表',
                '_page_btn_name'=>'添加新商品'
            ));
            $this->display();
        }

    /**
     * 删除商品
     */
    public function delete(){
        $model = D('Goods');
       if(FALSE !==$model->delete(I('get.id')))
           $this->success('删除成功！',U('lst'));
        else
            $this->error('删除失败！原因:'.$model->getError());
    }
}

?>