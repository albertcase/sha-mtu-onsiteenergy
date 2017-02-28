System Architecture
==========
from dirc 2017/2/20

#####管理员密码账号:
***账号:*** admin
***密码:*** samesame2016

#####系统简介:
***本微信管理系统采用symfony2.8，引入第三方库如下***

***1：***数据库操作库 joshcam/mysqli-database-class

****文档链接:**** https://github.com/joshcam/PHP-MySQLi-Database-Class

已经做了依赖注入，调用方式:$this->container->get('vendor.MysqliDb')


***2:***微信公众库 overtrue/wechat

****文档链接:**** https://github.com/overtrue/wechat   /  https://easywechat.org/

已经做了依赖注入，且配置完成：调用方式:$this->container->get('EasyWeChat.App')


***3:***doctine缓存类 Doctine/cache

****文档链接:**** http://doctrine-orm.readthedocs.io/projects/doctrine-orm/en/latest/reference/caching.html

已经做了依赖注入，且配置完成：调用方式:$this->container->get('my.Cache.Redis')


#####数据库表解析：
***数据如导入文件位置：***app/DB/database.sql

***表解析***

	| adp_article           | //文章保存群
	| analyse_shortvideo    | //用户消息处理结果表，报错段视频
	| file_path             | //文件对照表
	| request_analyse       | //用户消息处理结果表，总表
	| request_event         | //用户消息处理结果表，时间表
	| request_image         | //用户消息处理结果表，图片表
	| request_link          | //用户消息处理结果表，链接表
	| request_location      | //用户消息处理结果表，位置表
	| request_text          | //用户消息处理结果表，文本表
	| request_video         | //用户消息处理结果表，视频表
	| request_voice         | //用户消息处理结果表，音频表
	| stores                | //商店表
	| temp_event_log        | //限时时间日志表
	| user_premission       | //用户权限表
	| wechat_admin          | //用户管理表
	| wechat_events         | //用户事件表
	| wechat_feedbacks      | //被动消息回复表
	| wechat_getmsglog      | //用户消息接收总表
	| wechat_jssdk          | //jssdk表
	| wechat_keyword_tag    | //关键字表
	| wechat_material       | //图文表
	| wechat_material_news  | //sub图文表
	| wechat_menu           | //菜单表
	| wechat_menu_hierarchy | //菜单分层表
	| wechat_oauth          | //授权表
	| wechat_qrcode         | // qrcode表
	| wechat_users          | // 微信用户信息表


#####系统配置文件解析
***配置参数位置：*** /app/config/parameters.yml

***参数详情***

	# This file is auto-generated during the composer install
	parameters:
    	database_driver: pdo_mysql
    	database_host: 127.0.0.1
    	database_port: null
    	database_name: mtu_db
    	database_user: root
    	database_password: ''
    	locale: en
    	env(SYMFONY_SECRET): secret_value_for_symfony_demo_application
    	env(SYMFONY_LOG): '%kernel.logs_dir%/%kernel.environment%.log'
    	env(DATABASE_URL): 'sqlite:///%kernel.root_dir%/../var/data/blog.sqlite'
    	mailer_transport: smtp
    	mailer_host: 127.0.0.1
    	mailer_user: null
    	mailer_password: null

    # redis
    	redis_host: 127.0.0.1
    	redis_port: 6379
    	redis_prostr: mtu:

    # wechat config
    	wechat_Token: same_wechat_samesamechina
    	wechat_AppID: wxdffb071f687633b8
    	wechat_AppSecret: 8395e578d5d08499df2bbd6750a2f43e


    	bundles: //所有budle的id,当添加budle时需要添加，参与系统权限查找
      	- wechat_api
      	- article
      	- user

    	hostdomain: 127.0.0.1:8076


#####相关依赖注入service解释

	my.Wechat //早期微信接口封装类
	my.Wechat.Response //微信被动消息回复类
	cache.redis //redis存储类
	my.Cache.Redis //开源源doctine缓存类
	vendor.MysqliDb //开源mysql操作类
	vendor.LMysqliDb //使用长连接操作mysql
	my.dataSql //自写mysql操作扩展
	my.RedisLogic /自写redis类

#####相关依赖注入parameters解释
	session_login //登入session名称

	【bundle name】_permission
	    user_selfcontrol: user self control
      	user_usercontrol: Administrators
		权限名称: 权限简介

	【bundle name】_pages //页面rout 授权详情
		user_api_page_creatadmin:
			goto: user_out_notpassede //未拿到访问权限时显示页面rout name
        	permission: user_usercontrol //访问权限名称
        	login: user_page_login  //未登陆跳转rout name

	【bundle name】_papis //页面rout 授权详情
		user_api_papi_adminchangepw:
			goto: user_out_notpassede //未拿到访问权限时显示页面rout name
        	permission: user_usercontrol //访问权限名称

	 wechat_jscode_allowapi //jssdk授权api名称数组


#####被动消息回复结构
***主体类***   \Wechat\ApiBundle\Modals\classes\WechatResponse

***文件结构***

	|-- WechatResponse.php //被动回复主体类
	|-- WechatRequest //消息接收分发类文件位置
	|   |-- eventRequest  //事件消息接收类文件夹位置
	|   |   |-- clickEvent.php  类名称遵循  “事件名称”+“Event”
	|   |   |-- scanEvent.php
	|   |   |-- subscribeEvent.php
	|   |   `-- unsubscribeEvent.php
	|   |-- eventRequest.php //消息接收类  类名称遵循 “消息名称” + “Request”
	|   |-- locationRequest.php
	|   |-- RequestFormat.php //消息接收类规范
	|   `-- textRequest.php

***消息接收类相关规范***

****需要继承类 RequestFormat.php****

	<?php

	namespace Wechat\ApiBundle\Modals\classes\WechatRequest;

	class textRequest extends RequestFormat{

  		protected $feedbackdefault = true;  //当未找到匹配项时，是否回复default message ,默认为false

 	 	public function RequestFeedback(){  //该类必须实例化,为回复主体function
			some logic
  		}
  	}

##### rout 过滤规则

rout 访问控制是通过rout name进行控制

***1：***当rout名称中包含 “_page_“时进行页面过滤

****eg.:****

	 user_page_preference://权限参数
        goto: user_page_nopermission
        permission: user_selfcontrol
        login: user_page_login

    user_page_preference: //rout配置
    	path:     /user/preference
    	defaults: { _controller: UserBundle:Page:preference }

***2：***当rout名称中包含 “_papi_”时进行api过滤

****eg.:****

	user_api_papi_adminchangepw://权限配置
        goto: user_out_notpassede
        permission: user_usercontrol

    user_api_papi_adminchangepw: //rout配置
    	path:     /user/adminchangepw
    	defaults: { _controller: UserBundle:Api:adminchangepw }
