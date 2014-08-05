<?php
return array(
	//'配置项'=>'配置值'

    //数据库类型://用户名:密码@数据库地址:数据库端口/数据库名#字符集
    'DB_DSN'        => 'mysql://root:admin@localhost:3306/self-drug#utf8',

    'URL_MODEL'     => 0,

    //表单令牌验证相关的配置参数
    'TOKEN_ON'      =>    true,  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    '__hash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'    =>    'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    true,  //令牌验证出错后是否重置令牌 默认为true

    //域名
    'DOMAIN'        => array(
        'WWW'       => 'http://www.maimai.g/',
        'PUBLIC'    => 'http://www.maimai.g/public/',
        'UPLOAD'    => 'http://www.maimai.g/upload/',
    ),
    //路径
    'PATH'          => array(
        'UPLOAD'    =>  __REAL_ROOT__ . __DS__ . 'upload' . __DS__,   
    ),

);
