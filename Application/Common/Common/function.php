<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/7/21
 * Time: 20:09
 */
/**
 * 有选择性的过滤XSS --->性能非常低，尽量少用
 * @param $data
 */
function removeXSS($data){
    require_once './Htmlpurifier/HTMLPurifier.auto.php';
    $_clean_xss_config = HTMLPurifier_Config::createDefault();
    $_clean_xss_config->set('Core.Encoding', 'UTF-8');
    $_clean_xss_config->set('HTML.Allowed','div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]');
    $_clean_xss_config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
    $_clean_xss_config->set('HTML.TargetBlank', TRUE);
    $_clean_xss_obj = new HTMLPurifier($_clean_xss_config);
   return $_clean_xss_obj->purify($data);

}

/**
 * 上传图片
 * @param $imgName 图片名称
 * @param $dirName 子目录路径
 * @param array $thumb 图片规格
 * @return array
 */
function uploadOne($imgName, $dirName, $thumb = array())
{
    // 上传LOGO
    if(isset($_FILES[$imgName]) && $_FILES[$imgName]['error'] == 0)
    {
        $ic = C('IMAGE_CONFIG');
        $upload = new \Think\Upload(array(
            'rootPath' => $ic['rootPath'],
            'maxSize' => $ic['maxSize'],
            'exts' => $ic['exts'],
        ));// 实例化上传类
        $upload->savePath = $dirName . '/'; // 图片二级目录的名称
        // 上传文件
        // 上传时指定一个要上传的图片的名称，否则会把表单中所有的图片都处理，之后再想其他图片时就再找不到图片了
        $info   =   $upload->upload(array($imgName=>$_FILES[$imgName]));
        if(!$info)
        {
            return array(
                'ok' => 0,
                'error' => $upload->getError(),
            );
        }
        else
        {
            $ret['ok'] = 1;
            $ret['images'][0] = $logoName = $info[$imgName]['savepath'] . $info[$imgName]['savename'];
            // 判断是否生成缩略图
            if($thumb)
            {
                $image = new \Think\Image();
                // 循环生成缩略图
                foreach ($thumb as $k => $v)
                {
                    $ret['images'][$k+1] = $info[$imgName]['savepath'] . 'thumb_'.$k.'_' .$info[$imgName]['savename'];
                    // 打开要处理的图片
                    $image->open($ic['rootPath'].$logoName);
                    $image->thumb($v[0], $v[1])->save($ic['rootPath'].$ret['images'][$k+1]);
                }
            }
            return $ret;
        }
    }
}

/**
 * 显示图片
 * @param $url
 * @param string $width
 * @param string $height
 */
function showImage($url,$width="",$height=""){
    $url = C('IMAGE_CONFIG')['viewPath'].$url;
    if($width)
        $width = 'width='.$width;
    if($height)
        $height = 'height='.$height;

    echo "<image $width $height src='$url'/>";
}

/**
 * @param array $image 要删除的图片数组
 */
function deleteImage($image = array()){
    $savePath = C('IMAGE_CONFIG')['rootPath'];
    foreach($image as $v){
        unlink($savePath.$v);

    }

}