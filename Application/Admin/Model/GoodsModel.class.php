<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/7/21
 * Time: 01:43
 */

namespace Application\Admin\Model;


use Think\Model;

class GoodsModel extends Model {
//定义验证规则
    protected $_validate = array(
        array('goods_name','require','商品名称不能为空！',1),//1代表必须验证
        array('market_price','currency','市场价格必须是货币类型！',1),//1代表必须验证
        array('shop_price','currency','本店价格必须是货币类型！',1),//1代表必须验证

    );
}