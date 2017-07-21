<?php
namespace Admin\Model;

use Think\Model;
class GoodsModel extends Model
{
    //添加时允许接收的字段，这样可以防止前端输入一些错误的字段而导致错误，比如伪造ID等等
    protected  $insertFields = 'goods_name,market_price,shop_price,is_on_sale,goods_desc';
   //定义验证规则
	protected $_validate = array(
		array('goods_name', 'require', '商品名称不能为空！', 1),
		array('market_price', 'currency', '市场价格必须是货币类型！', 1),
		array('shop_price', 'currency', '本店价格必须是货币类型！', 1),
	);

    /**
     * 钩子函数，添加之前的调用函数
     * @param $data,即将插入到数据库中的数据
     * @param $option
     * &按引用传递，内部函数要修改外部函数的变量必须使用引用函数，除非修改的是对象
     */
    protected function _before_insert(&$data,$option){
        //获取当前时间
       $data['addtime'] = date('Y-m-d H:i:s',time());
    }
}

?>