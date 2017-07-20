<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/7/21
 * Time: 01:42
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsController extends Controller {


    /*
         * 显示和处理表单
         */
    public function add(){
        //判断用户是否提交了表单
        if(IS_POST){
            $model = D('goods');
            //接收表单
            /**
             * 第一参数：要接收的数据默认是$_post
             * 第二参数：表单的类型，当前是添加还是修改表单，1.添加，2.修改
             * I('post.'):过滤参数，防止xss攻击
             * 验证表单
             */
            if($model->create(I('post.'),1)){
                if($model->add()){
                    $this->success('操作成功！',U('lst'));
                    exit;//一定要加exit,要不还会执行后面的数据
                }

            }
            //上面处理失败后，下面处理失败的请求
            //失败的原因
            $error = $model->getError();
            //由控制器显示信息,并在3秒跳回上一个页面
            $this->error($error);

        }
        //显示表单
        $this->display();
    }
    public function lst(){
        echo '商品列表';
    }
}