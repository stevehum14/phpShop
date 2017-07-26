<?php
return array(
    /* 数据库设置 */
    'DB_TYPE'    =>  'mysqli',     // 数据库类型
    'DB_HOST'    =>  'localhost', // 服务器地址
    'DB_NAME'    =>  'eShop',          // 数据库名
    'DB_USER'    =>  'root',      // 用户名
    'DB_PWD'     =>  'root',          // 密码
    'DB_PORT'    =>  '8889',        // 端口
    'DB_PREFIX'  =>  'es_',    // 数据库表前缀
    'DB_CHARSET' =>  'utf8',      // 数据库编码默认采用utf8
    'DEFAULT_FILTER'=>'trim,htmlspecialchars',//默认用于I函数

    //**********图片的相关配置*******************
    'IMAGE_CONFIG'=>array(
        'maxSize'=>1024*1014,
        'exts'=>array('jpg', 'gif', 'png', 'jpeg'),
        'rootPath'=>'./Public/Uploads/',//上传图片保存的路径
        'viewPath'=>'/Public/Uploads/',//显示图片的路径
    ),


);