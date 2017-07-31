<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/7/29
 * Time: 10:45
 */

namespace Admin\Model;


use Think\Model;

class MemberLevelModel extends Model {
    //添加前过滤接收字段
    protected $insertFields = 'level_name,jifen_bottom,jifen_top';
    //更新前过滤接收字段
    protected $updateFields = 'id,level_name,jifen_bottom,jifen_top';
    protected  $_validate = array(
        array('level_name', 'require', '级别名称不能为空！', 1, 'regex', 3),
        array('level_name', '1,30', '级别名称的值最长不能超过 30 个字符！', 1, 'length', 3),
        array('jifen_bottom', 'require', '积分下限不能为空！', 1, 'regex', 3),
        array('jifen_bottom', 'number', '积分下限必须是一个整数！', 1, 'regex', 3),
        array('jifen_top', 'require', '积分上限不能为空！', 1, 'regex', 3),
        array('jifen_top', 'number', '积分上限必须是一个整数！', 1, 'regex', 3),
    );
    public function search($pageSzie = 20){
        $where = array();
        //取得查询条件的个数
        $count = $this->alias('a')->where($where)->count();
        $page = new \Think\Page($count,$pageSzie);
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $data['page'] = $page->show();
        //取得数据
        $data['data'] = $this->alias('a')->where($where)->group('a.id')->limit($page->firstRow.','.$page->listRows)->select();
        return $data;

    }
    protected function _before_insert(&$data,$option){

    }
    protected function _before_update(&$data,$option){

    }
    protected function _before_delete($option){
        $id = $option['where']['id'];
        if(is_array($id)){
            $this->error = '不支持批量删除';
            return FALSE;
        }
    }

}