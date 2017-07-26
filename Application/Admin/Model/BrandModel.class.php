<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/7/26
 * Time: 06:22
 */

namespace Admin\Model;


use Think\Model;

class BrandModel extends Model
{
    //添加时允许接收的字段
    protected $insertFields = 'brand_name,site_url,logo';
    //更新时允许接收的字段
    protected $updateFields = 'id,brand_name,site_url,logo';
    //验证字段
    protected $_validate = array(
        array('brand_name', 'require', '品牌名称不能为空！', 1, 'regex', 3),
        array('brand_name', '1,30', '品牌名称的值最长不能超过 30 个字符！', 1, 'length', 3),
        array('site_url', '1,150', '官方网址的值最长不能超过 150 个字符！', 2, 'length', 3),
    );

    /**添加前
     * @param $data
     * @param $option
     * @return bool
     */
    protected function _before_insert(&$data,$option){
        if($_FILES['logo'] && $_FILES['logo']['error'] == 0){
            $ret = uploadOne('logo','Brand',array());
            if($ret['ok'] == 1){
                $data['logo'] = $ret['images'][0];
            }else{
                $this->error = $ret['error'];
                return false;
            }

        }

    }
/*
 * 搜索
 */
    public function search($pageSize = 20){
        $where = array();
        if($brand_name = I('get.brand_name'))
            $where['brand_name'] = array('like',"%$brand_name%");
        $count = $this->alias('a')->where($where)->count();
        $page = new \Think\Page($count,$pageSize);
        //设置翻页样式
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $data['page'] = $page->show();
        //**************取数据*******************
        $data['data'] = $this->alias('a')->where($where)->group('a.id')->limit($page->firstRow.','.$page->listRows)->select();
        return $data;

    }
    /**更新前
     * @param $data
     * @param $option
     * @return bool
     */
    protected function _before_update(&$data,$option){
        //添加图片
        $id = $option['where']['id'];
        if($_FILES['logo'] && $_FILES['logo']['error'] == 0){
            $ret = uploadOne('logo','Brand',array());
            if($ret['ok'] == 1){
                $data['logo'] = $ret['images'][0];
            }else{
                $this->error = $ret['error'];
                return false;
            }
            $oldImages = $this->field('logo')->find($id);
            deleteImage($oldImages);

        }
    }
    protected function _before_delete($option){
        $id = $option['where']['id'];
        if(is_array($id)){
            $this->error = '不支持批量删除';
            return FALSE;
        }
        $images = $this->field('logo')->find($id);
        deleteImage($images);
    }
}

