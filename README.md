Wechat System
==========

A Symfony project created on October 15, 2015, 7:45 am.

#### Wechat user status Api:
***URL***: /wechat/user/status

***params***
    {
         openid: OPENID
    }

***Request Method:*** GET

    {
    "code": "9",//关注过，但已经取消关注
    "msg": "this openid already unsubscribed"
    }
    //or
    {
    "code": "10",//已经关注
    "msg": "this openid already subscribed"
    }
    //or
    {
        "code": "8",//从未关注
        "msg": "this openid not subscribed"
    }

#### Wechat oauth Api
***Oauth Url***: /v1/wx/web/oauth2/authorize

***params***

    {
      redirect_uri: REDIRECT_URI //REDIRECT_URI need urlencode
      scope: 授权方式  snsapi_userinfo/snsapi_base
    }

***Request Method:*** GET

***Result Back:***

	{
		access_token：ACCESS_TOKEN
		openid: OPENID
	}
当需要授权的页面调转至/v1/wx/web/oauth2/authorize后，在系统中完成授权后跳转至****REDIRECT_URI****并且带上****access_token****和****openid****；

****tips:****

	****access_token****为通过code换取网页授权的access_token

***eg:***
****当授权需要授权的页面为：****http://dirc.samesamechina.com/test/oauth?aa=aa&bb=bb

****跳转授权页面为：****http://dirc.samesamechina.com/v1/wx/web/oauth2/authorize?redirect_uri=****urlnecode(**** http://dirc.samesamechina.com/test/oauth?aa=aa&bb=bb ****)****&scope=snsapi_userinfo

****授权成功后：****跳回页面  http://dirc.samesamechina.com/test/oauth?access_token=ACCESS_TOKEN&openid=OPENID&aa=aa&bb=bb

****获取用户信息：****第三方自行使用access_token和openid调用微信接口获取用户信息

****获取用户信息程序例子如下****

	<?php
      $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN';//wechat api
      $url = str_replace('ACCESS_TOKEN', $access_token, $url);
      $url = str_replace('OPENID', $openid, $url);
      $wechatuser_info = file_get_contents($url);
    ?>
    
 