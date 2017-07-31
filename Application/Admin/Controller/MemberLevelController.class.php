<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/7/29
 * Time: 09:59
 */

namespace Admin\Controller;


use Think\Controller;

class MemberLevelController extends Controller  {
    /**
     * 添加会员级别
     */
    public function add(){
        if(IS_POST){
            $model = D('MemberLevel');
            if($model->create(I('post.'),1)){
                if($id = $model->add()){
                    $this->success('添加成功！',U('lst?p='.I('get.p')));
                    exit;
                }
            }
            $this->error($model->getError());
        }
    // 设置页面中的信息
        $this->assign(array(
            '_page_title' => '添加会员级别',
            '_page_btn_name' => '会员级别列表',
            '_page_btn_link' => U('lst'),
        ));
        $this->display();

    }
    /*
     * 编辑会员级别
     */
    public function edit(){
        $id = I('get.id');
        $model = D('MemberLevel');
        if(IS_POST){
            if($model->create(I('post.'),2)){
                if($model->save() !==false){
                    $this->success('添加成功！',U('lst?p='.I('get.p',1)));
                    exit;
                }
            }
            $this->error($model->getError());
        }
        //设置页面中的信息
        $data = $model->find($id);
        $this->assign('data',$data);
        // 设置页面中的信息
        $this->assign(array(
            '_page_title' => '修改会员级别',
            '_page_btn_name' => '会员级别列表',
            '_page_btn_link' => U('lst'),
        ));
        $this->display();
    }

    /**
     * 删除会员级别
     */
    public function delete()
    {
        $model = D('MemberLevel');
        if($model->delete(I('get.id', 0)) !== FALSE)
        {
            $this->success('删除成功！', U('lst', array('p' => I('get.p', 1))));
            exit;
        }
        else
        {
            $this->error($model->getError());
        }
    }
    public function lst()
    {
        $model = D('MemberLevel');
        $data = $model->search();
        $this->assign(array(
            'data' => $data['data'],
            'page' => $data['page'],
        ));

        // 设置页面中的信息
        $this->assign(array(
            '_page_title' => '会员级别列表',
            '_page_btn_name' => '添加会员级别',
            '_page_btn_link' => U('add'),
        ));
        $this->display();
    }

}