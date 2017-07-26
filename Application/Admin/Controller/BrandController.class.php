<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/7/26
 * Time: 06:23
 */

namespace Admin\Controller;


use Think\Controller;

class BrandController extends Controller {
    /**
     * 添加品牌
     */
    public function add(){
        $model = D('Brand');
        if(IS_POST){
            if($model->create(I('post.'),1)){
                if($id = $model->add()){
                    $this->success('添加成功',U('lst?p='.I('get.p')));
                    exit;
                }
            }
            $this->error($model->getError());
        }
        // 设置页面中的信息
        $this->assign(array(
            '_page_title' => '添加品牌',
            '_page_btn_name' => '品牌列表',
            '_page_btn_link' => U('lst'),
        ));
        $this->display();

    }

    /**
     * 修改品牌
     */
    public function edit(){
        $id = I('get.id');
        if(IS_POST){
            $model = D('Brand');
            if($model->create(I('post.'),2)){
                if($model->save() !==FALSE){
                    $this->success('修改成功！',U('lst',array('p'=>I('get.p',1))));
                    exit;
                }
            }
            $this->error($model->getError());
        }
        $model = M('Brand');
        $data = $model->find($id);
        $this->assign('data',$data);
        // 设置页面中的信息
        $this->assign(array(
            '_page_title' => '修改品牌',
            '_page_btn_name' => '品牌列表',
            '_page_btn_link' => U('lst'),
        ));
        $this->display();
    }

    /**
     * 删除品牌
     */
    public function delete(){
        $id = I('get.id');
        $model = D('Brand');
        if($model->delete($id) !== FALSE){
            $this->success('删除成功！',U('lst',array('p'=>I('get.p',1))));
            exit;
        }else{
            $this->error($model->getError());
        }

    }
    public function lst(){
        $model = D('Brand');
        $data = $model->search();
        $this->assign($data);
        // 设置页面中的信息
        $this->assign(array(
            '_page_title' => '品牌列表',
            '_page_btn_name' => '添加品牌',
            '_page_btn_link' => U('add'),
        ));
        $this->display();
    }


}