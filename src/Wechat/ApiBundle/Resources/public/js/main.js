var formstr = {//comfirm string
  temail:function(str){
      var temail = new RegExp("^[0-9a-zA-Z._-]+@[0-9a-zA-Z-]+\.[0-9a-zA-Z]+(\.[0-9a-zA-Z]+){0,1}$");
      if(temail.test(str)){
	return true;
      }
      return false;
  },
  tmenuname:function(str){/*检验按钮名称长度1-16(中文8位，英文16位)*/
      var byteLen = 0, len = str.length;
      if( !str ) return 0;
      for( var i=0; i<len; i++ )
	  byteLen += str.charCodeAt(i) > 255 ? 2 : 1;
      if( byteLen > 0&& byteLen <=16)
	    return true;
      return false;
  },
  turl:function(str){
      var tname = new RegExp("^.{1,1000}$");
      if(tname.test(str)){
	return true;
      }
      return false;
  },
  ttype:function(str){
    var tname = new RegExp("^.{1,50}$");
    if(tname.test(str)){
	if(str != "0")
	      return true;
      }
      return false;
  },
  tname:function(str){
    var tname = new RegExp("^.{1,20}$");
    if(tname.test(str)){
	return true;
      }
      return false;
  },
  tnonull:function(str){
    var tname = new RegExp("^.{1,50}$");
    if(tname.test(str)){
	return true;
      }
      return false;
  },
  tnonull2:function(str){
    var tname = new RegExp("^.{1,1000}$");
    if(tname.test(str)){
	return true;
      }
      return false;
  },
  tinfostr:function(str){
    var tname = new RegExp("^.{8,100}$");
    if(tname.test(str)){
	return true;
      }
      return false;
  },
  ttoken:function(str){
     var tname = new RegExp("^[0-9a-zA-Z]{3,32}$");
     if(tname.test(str)){
	return true;
      }
      return false;
  },
  tAesKey:function(str){
    var tname = new RegExp("^[0-9a-zA-Z]{43}$");
     if(tname.test(str)){
	return true;
      }
      return false;
  },
  tipstr:function(str,chk,tip){
    var t = formstr[chk](str);
    if(!t)
      popup.openwarning(tip);
    return t;
  },
  tipstrobj:function(obj,chk,tip){
    var str = obj.val();
    var t = formstr[chk](str);
    if(!t){
      popup.openwarning(tip);
      obj.parent().addClass("has-error");
    }
    return t;
  },
  tall:function(data){
    var la = data.length;
    var self = this;
    var a = true;
    for(var i='0'; i<la; i++){
      a = a && self.tipstr(data[i]["0"],data[i]["1"],data[i]["2"]);
    }
    return a;
  },
  tallobj:function(data){
    $(".has-error").each(function(){
      $(this).removeClass("has-error");
    });
    var la = data.length;
    var self = this;
    var a = true;
    for(var i='0'; i<la; i++){
      a = self.tipstrobj(data[i]["0"], data[i]["1"], data[i]["2"]) && a;
    }
    return a;
  }
}


var mouseinfo = {
  mouseL: null,
  mouseT: null,
  initMouse: function(e){
    this.mouseL = e.clientX;
    this.mouseT = e.clientY;
  }
}

var fileupload = {
  sendfiles:function(data, obj){
	var self=this;
  popup.openprogress();
	var formData = new FormData();
	var xhr = new XMLHttpRequest();
	formData.append("uploadfile",data);
	xhr.open ('POST',"/wechat/uploadimage");
	xhr.onload = function(event) {
    popup.closeprogress();
    if (xhr.status === 200) {
      var aa = JSON.parse(xhr.responseText);
      if(aa.code == '10'){
        fileupload.replaceinput(aa.path,obj);
        popup.openwarning('upload success');
        return true;
      }
      popup.openwarning(aa.msg);
    } else {
      popup.openwarning('upload error');
    }
  };
    xhr.upload.onprogress = self.updateProgress;
    xhr.send (formData);
  },
  updateProgress:function(event){
    if (event.lengthComputable){
        var percentComplete = event.loaded;
        var percentCompletea = event.total;
        var press = (percentComplete*100/percentCompletea).toFixed(2);//onprogress show
      	popup.goprogress(press);
    }
  },
  replaceinput:function(url ,obj){
    var a= '<i class="fa fa-times"></i><img src="'+url+'" style="width:200px;display:block;" class="newspic">';
    obj.after(a);
    obj.remove();
  },
  replaceimage:function(obj){
    var a = '<input type="file" name="uploadfile" class="newsfile">';
    obj.next().remove();
    obj.after(a);
    obj.remove();
  }
}

var popup = {
  warningshow : function(selector,text) {
    var self = this;
    var warning = $(document.createElement('div'));
    warning.addClass('warningbox').append(text);
    warning.prependTo($(selector)).hide().slideDown();
    warning.one('click', function() {
      self.warningautomove($(this));
    });
    self.warningclear(warning);
    return true;
  },
  warningautomove: function(obj) {
    obj.fadeOut(function(){
      this.remove();
    });
  },
  warningclear : function(element) {
    setTimeout(this.warningautomove, '3000', element);
  },
  openprogress:function(){
    $("#myprogress").show();
  },
  closeprogress:function(){
    $("#myprogress").hide();
  },
  goprogress:function(t){
    $("#myprogress .progress-bar").attr("aria-valuenow" ,t);
    $("#myprogress .progress-bar").css("width", t+"%");
    $("#myprogress .sr-only").text(t+"%");
  },
  openwarning:function(text){
    this.warningshow('#warningpopup',text);
  },
  opencomfirm:function(text,fun){
    var a = '<div>'+text+'</div>';
    a += '<div>';
    a += '<button type="button" onclick="popup.closecomfirm()" class="btn btn-default btn-sm">CANCEL</button>&nbsp;&nbsp;';
    a += '<button type="button" onclick="'+fun+'" class="btn btn-primary btn-sm">TRUE</button>';
    a += '</div>';
    $("#comfirmpopup > .comfirmpopup").html(a);
    $("#comfirmpopup").show();
  },
  closecomfirm:function(){
    $("#comfirmpopup>.comfirmpopu").empty();
    $("#comfirmpopup").hide();
  },
  openloading:function(){
    $("#loadingpopup").show();
  },
  closeloading:function(){
    $("#loadingpopup").hide();
  }
}


var htmlconetnt = {
  mediachoose: "",
  ObjMedialist: null,
  ObjMedialistSub: null,
  ObjMedialistEdit: null,
  ajaxloadmedialist: function(itime){
    popup.openloading();
    var pdata = {};
    if(itime)
      pdata = {
        timestamp: itime,
        order: "bottom"
      };
    $.ajax({
      url:"/wechat/newsget",
      type:"post",
      dataType:'json',
      data: pdata,
      success:function(data){
        popup.closeloading();
        if(data.code == '10'){
          var obj = "ObjMedialist" + htmlconetnt.mediachoose;
          $(htmlconetnt[obj]['0']).append(htmlconetnt.mdeiacontent(data.list));
        }
        popup.openwarning(data.msg);
      },
      error:function(){
        popup.closeloading();
        popup.openwarning('unknow errors');
      }
    });
  },
  mdeiacontent: function(data){
    var a = "";
    var obj = "ObjMedialist" + htmlconetnt.mediachoose;
    var contentname = "msgnewradio" + htmlconetnt.mediachoose;
    for (var x in data){
      if(htmlconetnt[obj].children("[newsid='" + x + "']").length)
       continue;
      var la = data[x].length;
      var updatetime = 0;
      if(la>0)
        updatetime = data[x]['0']["update_time"];
      a += '<div class="msg-mediacard" newstime="'+updatetime+'" newsid="' + x + '">';
      a += '<input type="radio" class="msgnewradio"  name="' + contentname + '" id="r'+ contentname + x+'" value="'+x+'" />';
      a += '<div class="msgnewul">';
      a += '<ul>';
      if(la>0){
        a += '<li style="background-repeat: no-repeat;background-size:cover;background-image:url('+"'/cimg.php?style=w_400&amp;image="+encodeURIComponent(data[x]['0']['thumb_url'])+"'"+')">';
        a += '<p>'+data[x]['0']['title']+'</p>';
        a += '<a href="'+data[x]['0']['url']+'" target="_blank">点击预览</a>';
        a += '</li>';
      }
      for(var i=1; i<la; i++){
        a += '<li>'
        a += '<p>'+data[x][i]['title']+'</p>';
        a += '<img src="/cimg.php?style=w_400&amp;image='+encodeURIComponent(data[x][i]['thumb_url'])+'">';
        a += '<a href="'+data[x][i]['url']+'" target="_blank">点击预览</a>';
        a += '</li>';
      }
      a += '</ul>';
      a += '</div>';
      a += '<div class="msgnewbutton"><label for="r' + contentname + x+'" class="label"><i class="fa fa-check"></i><i class="msg-check">Pick</i></button></div>';
      a += '</div>';
    }
    return a;
  },
  amediamessage: function(info){
    htmlconetnt.mediachoose = "Edit";
    var a = '<div class="msg-conetnt"></div>';
    a += '<button class="msg-renew btn btn-default btn-xs"><i class="fa fa-chevron-down"></i></button>';
    var obj = "ObjMedialist" + htmlconetnt.mediachoose;
    htmlconetnt[obj] = $(a);
    $(htmlconetnt[obj]['1']).click(function(){
      var obj = "ObjMedialist" + htmlconetnt.mediachoose;
      var itime = $(htmlconetnt[obj]['0']).children('.msg-mediacard').last().attr('newstime');
      htmlconetnt.ajaxloadmedialist(itime);
    });
    $(htmlconetnt[obj]['0']).append(htmlconetnt.mdeiacontent(info));
    $(htmlconetnt[obj]['0']).children(".msg-mediacard").eq(0).attr("newstime", "9999999999");
    $(htmlconetnt[obj]['0']).find("input").eq(0).prop("checked", true);
    return htmlconetnt[obj];
  },
  mediamessage: function(name){
    htmlconetnt.mediachoose = "";
    if(name)
      htmlconetnt.mediachoose = name;
    var obj = "ObjMedialist" + htmlconetnt.mediachoose;
    if(!htmlconetnt[obj]){
      var a = '<div class="msg-conetnt"></div>';
      a += '<button class="msg-renew btn btn-default btn-xs"><i class="fa fa-chevron-down"></i></button>';
      htmlconetnt[obj] = $(a);
      htmlconetnt.ajaxloadmedialist();
    }
    $(htmlconetnt[obj]['1']).click(function(){
      var obj = "ObjMedialist" + htmlconetnt.mediachoose;
      var itime = $(htmlconetnt[obj]['0']).children('.msg-mediacard').last().attr('newstime');
      htmlconetnt.ajaxloadmedialist(itime);
    });
    return htmlconetnt[obj];
  },
  locationmessage: function(){
    var a = "<br><b>Send your location</b>";
    return a;
  },
  none:function(){
    var a = '';
    return a;
  },
  externalpage:function(){
    var a = '<br>';
    a += '<div class="form-group">';
    a += '<label>Redirect to:</label>';
    a += '<input class="form-control viewurl" placeholder="Enter Your Url" style="width:90%">';
    a += '</div>';
    return a;
  },
  aview:function(content){
    var a = '<br>';
    a += '<div class="form-group">';
    a += '<label>Redirect to:</label>';
    a += '<input class="form-control viewurl" placeholder="Enter Your Url" style="width:90%" value="'+content+'">';
    a += '</div>';
    return a;
  },
  pushmessage:function(){
    var a = '<br>';
        a += '<div class="newslist">';
        a += '<i class="fa fa-minus-square" style="color:red"></i>';
        a += '<div class="form-group">';
        a += '<label>Title:</label>';
        a += '<input class="form-control newstitle" placeholder="Enter TITLE" style="width:90%">';
        a += '</div>';
        a += '<div class="form-group">';
        a += '<label>Description:</label>';
        a += '<input class="form-control newsdescription" placeholder="Enter Your Description" style="width:90%">';
        a += '</div>';
        a += '<div class="form-group">';
        a += '<label>Link:</label>';
        a += '<input class="form-control newslink" placeholder="Enter Your Url" style="width:90%" name="link">';
        a += '</div>';
        a += '<div class="form-group">';
        a += '<label>Cover:</label>';
        a += '<input type="file" name="uploadfile" class="newsfile">';
        a += '</div>';
        a += '<hr>';
        a += '</div>';
        a += '<i class="fa fa-plus-square" style="color:green"></i>';
        a += '<button type="button" style="margin-left:10px" class="btn btn-primary btn-xs mytipsboxcs">From Online<span style="position:relative"></span></button>';
    var aobj = $(a);
    $(aobj[3]).click(function(e){
      mytipsbox.insertnewsP = $(this);
      mytipsbox.selecttipbox();
      e.stopPropagation();
    });
    return aobj;
  },
  loadpushmessage:function(data){
    var a = '';
        a += '<div class="newslist">';
        a += '<i class="fa fa-minus-square" style="color:red"></i>';
        a += '<div class="form-group">';
        a += '<label>Title:</label>';
        a += '<input class="form-control newstitle" placeholder="Enter TITLE" value="'+data.Title+'" style="width:90%">';
        a += '</div>';
        a += '<div class="form-group">';
        a += '<label>Description:</label>';
        a += '<input class="form-control newsdescription" placeholder="Enter Your Description" value="'+data.Description+'" style="width:90%">';
        a += '</div>';
        a += '<div class="form-group">';
        a += '<label>Link:</label>';
        a += '<input class="form-control newslink" placeholder="Enter Your Url" style="width:90%" value="'+data.Url+'" name="link">';
        a += '</div>';
        a += '<div class="form-group">';
        a += '<label>Cover:</label>';
        a += '<i class="fa fa-times"></i><img src="'+data.PicUrl+'" style="width:200px;display:block;" class="newspic">';
        a += '</div>';
        a += '<hr>';
        a += '</div>';
        a += '<i class="fa fa-plus-square" style="color:green"></i>';
        a += '<button type="button" style="margin-left:10px" class="btn btn-primary btn-xs mytipsboxcs">From Online<span></span></button>';
    var aobj = $(a);
    $(aobj[2]).click(function(e){
      mytipsbox.insertnewsP = $(this);
      mytipsbox.selecttipbox();
      e.stopPropagation();
    });
    return aobj;
  },
  apushmessage:function(data){
    var la = data.length;
    var a = "<br>";
    for(var i = 0 ;i<la ;i++){
      a += '<div class="newslist">';
      a += '<i class="fa fa-minus-square" style="color:red"></i>';
      a += '<div class="form-group">';
      a += '<label>Title:</label>';
      a += '<input class="form-control newstitle" placeholder="Enter TITLE" style="width:90%" value="'+data[i]['Title']+'">';
      a += '</div>';
      a += '<div class="form-group">';
      a += '<label>Description:</label>';
      a += '<input class="form-control newsdescription" placeholder="Enter Your Description" style="width:90%" value="'+data[i]['Description']+'">';
      a += '</div>';
      a += '<div class="form-group">';
      a += '<label>Link:</label>';
      a += '<input class="form-control newslink" placeholder="Enter Your Url" style="width:90%" name="link" value="'+data[i]['Url']+'">';
      a += '</div>';
      a += '<div class="form-group">';
      a += '<label>Cover:</label>';
      a += '<i class="fa fa-times"></i><img src="'+data[i]['PicUrl']+'" style="width:200px;display:block;" class="newspic">';
      a += '</div>';
      a += '<hr>';
      a += '</div>';
    }
    if(la < 10){
      a += '<i class="fa fa-plus-square" style="color:green"></i>';
      a += '<button type="button" style="margin-left:10px" class="btn btn-primary btn-xs mytipsboxcs">From Online<span></span></button>';
    }
    var aobj = $(a);
    aobj.each(function(){
      if($(this).is(".mytipsboxcs")){
        $(this).click(function(e){
          mytipsbox.insertnewsP = $(this);
          mytipsbox.selecttipbox();
          e.stopPropagation();
        });
      }
    });
    return aobj;
  },
  textmessage:function(){
      var a = '<br>';
        a += '<div class="form-group">';
        a += '<label>MESSAGE</label>';
        a += '<textarea class="form-control textcontent" rows="3"></textarea>';
        a += '</div>';
    return a;
  },
  atextmessage:function(conetnt){
      var a = '<br>';
        a += '<div class="form-group">';
        a += '<label>MESSAGE</label>';
        a += '<textarea class="form-control textcontent" rows="3">'+conetnt+'</textarea>';
        a += '</div>';
    return a;
  },
  addnewshtml:function(){
    var a = '<div class="newslist">';
        a += '<i class="fa fa-minus-square" style="color:red"></i>';
        a += '<div class="form-group">';
        a += '<label>Title:</label>';
        a += '<input class="form-control newstitle" placeholder="Enter TITLE" style="width:90%">';
        a += '</div>';
        a += '<div class="form-group">';
        a += '<label>Description:</label>';
        a += '<input class="form-control newsdescription" placeholder="Enter Your Description" style="width:90%">';
        a += '</div>';
        a += '<div class="form-group">';
        a += '<label>Link:</label>';
        a += '<input class="form-control newslink" placeholder="Enter Your Url" style="width:90%" name="link">';
        a += '</div>';
        a += '<div class="form-group">';
        a += '<label>Cover:</label>';
        a += '<input type="file" name="uploadfile" class="newsfile">';
        a += '</div>';
        a += '<hr>';
        a += '</div>';
        a += '<i class="fa fa-plus-square" style="color:green"></i>';
        a += '<button type="button" style="margin-left:10px" class="btn btn-primary btn-xs mytipsboxcs">From Online<span></span></button>';
    var aobj = $(a);
    $(aobj[2]).click(function(e){
      mytipsbox.insertnewsP = $(this);
      mytipsbox.selecttipbox();
      e.stopPropagation();
    });
    return aobj;
  },
  tagkeyword:function(){
    var a = '<div class="form-group">';
        a += '<label>Keyword:</label>';
        a += '<i class="fa fa-minus-circle"></i>';
        a += '<input class="form-control inputkeyword" placeholder="Enter Keyword" style="width:80%">';
        a += '</div>';
    return a;
  },
  showtagkeyword:function(data){
    var la = data.length;
    var a = "";
    for(var i=0;i<la; i++){
      a += '<div class="form-group">';
      a += '<label>Keyword:</label>';
      a += '<i class="fa fa-minus-circle"></i>';
      a += '<input class="form-control inputkeyword" placeholder="Enter Keyword" style="width:80%" value="'+data[i]+'">';
      a += '</div>';
    }
    a +='<i class="fa fa-plus-circle"></i>';
    return a;
  },
  belongtohtml:function(data){
    var a = "";
    var la = data.length;
    for (var i in data){
      a += '<option value="'+i+'">'+data[i]+'</option>';
    }
    return a;
  }
}

var menu = {
  mbuttonfun:null,
  subbuttonfun:null,
  editbuttonfun:null,
  editinfo:null,
  delobj:null,
  edithidden: 0,
  showfeedback: function(obj){//add menu
    var self = this;
    var action = obj.attr('action');
    if($("#myModal ."+action+" div").length == 0)
      $("#myModal ."+action).html(htmlconetnt[action]());
    $("#myModal .menushow").removeClass("menushow");
    $("#myModal ."+action).addClass("menushow");
    self.mbuttonfun = "m"+action;
  },
  showfeedback2: function(obj){//add submenu
    var self = this;
    var action = obj.attr('action');
    if($("#submenu ."+action+" div").length == 0)
      $("#submenu ."+action).html(htmlconetnt[action]("Sub"));
    $("#submenu .menushow").removeClass("menushow");
    $("#submenu ."+action).addClass("menushow");
    self.subbuttonfun = "sub"+action;
  },
  showfeedback3: function(obj){//add submenu
    var self = this;
    var action = obj.attr('action');
    if($("#editmenu ."+action+" div").length == 0)
      $("#editmenu ."+action).html(htmlconetnt[action]("Edit"));
    $("#editmenu .menushow").removeClass("menushow");
    $("#editmenu ."+action).addClass("menushow");
    self.editbuttonfun = "edit"+action;
  },
  mmediamessage: function(){//AAAAAAAAAAAAA
    var mid = $("#myModal .msgnewradio:checked").val();
    if(!mid)
      mid = "";
    var a={
      "buttonaddm[menuName]": $("#myModal .menuname").val(),
      "buttonaddm[eventtype]": 'media_id',
      "buttonaddm[eventmedia_id]": mid,
    };
    return a;
  },
  mnone:function(){
    var a={
      "buttonaddm[menuName]": $("#myModal .menuname").val(),
    };
    return a;
  },
  mexternalpage:function(){
    var a={
      "buttonaddm[menuName]": $("#myModal .menuname").val(),
      "buttonaddm[eventtype]": 'view',
      "buttonaddm[eventUrl]": $("#myModal .viewurl").val(),
    };
    return a;
  },
  mpushmessage:function(){
    var self = this;
    var key = new Date().getTime();
    var a = {
      "buttonaddm[menuName]": $("#myModal .menuname").val(),
      "buttonaddm[eventtype]": 'click',
      "buttonaddm[MsgType]": 'news',
      "buttonaddm[eventKey]": "e"+key,
      "buttonaddm[newslist]": self.getnewslist($("#myModal .pushmessage .newslist")),
    };
    return a;
  },
  getnewslist:function(obj){
    var a = [];
    var la = obj.length;
    obj.each(function(){
      var mself = $(this);
      var b = {};
      b = {
        "Title": mself.find(".newstitle").val(),
        "Description": mself.find(".newsdescription").val(),
        "Url": mself.find(".newslink").val(),
        "PicUrl": mself.find(".newspic").attr("src"),
      }
      a.push(b);
    });
    return JSON.stringify(a);
  },
  mtextmessage:function(){
    var key = new Date().getTime();
    var a={
      "buttonaddm[menuName]":$("#myModal .menuname").val(),
      "buttonaddm[eventtype]":'click',
      "buttonaddm[Content]": $("#myModal .textcontent").val(),
      "buttonaddm[MsgType]": 'text',
      "buttonaddm[eventKey]": "e"+key,
    };
    return a;
  },
  mlocationmessage:function(){
    var key = new Date().getTime();
    var a={
      "buttonaddm[menuName]":$("#myModal .menuname").val(),
      "buttonaddm[eventtype]":'location_select',
      "buttonaddm[eventKey]": "e"+key,
    }
    return a;
  },
  subnone:function(){
    var a={
      "buttonaddsub[menuName]": $("#submenu .menuname").val(),
      "buttonaddsub[mOrder]": $("#submenu .belongto").val(),
    };
    return a;
  },
  subexternalpage:function(){
    var a={
      "buttonaddsub[menuName]": $("#submenu .menuname").val(),
      "buttonaddsub[mOrder]": $("#submenu .belongto").val(),
      "buttonaddsub[eventtype]": 'view',
      "buttonaddsub[eventUrl]": $("#submenu .viewurl").val(),
    };
    return a;
  },
  submediamessage: function(){
    var mid = $("#submenu .msgnewradio:checked").val();
    if(!mid)
      mid = "";
    var a={
      "buttonaddsub[menuName]": $("#submenu .menuname").val(),
      "buttonaddsub[mOrder]": $("#submenu .belongto").val(),
      "buttonaddsub[eventtype]": 'media_id',
      "buttonaddsub[eventmedia_id]": mid,
    };
    return a;
  },
  subpushmessage:function(){
    var self = this;
    var key = new Date().getTime();
    var a = {
      "buttonaddsub[menuName]": $("#submenu .menuname").val(),
      "buttonaddsub[mOrder]": $("#submenu .belongto").val(),
      "buttonaddsub[eventtype]": 'click',
      "buttonaddsub[MsgType]": 'news',
      "buttonaddsub[eventKey]": "e"+key,
      "buttonaddsub[newslist]": self.getnewslist($("#submenu .pushmessage .newslist")),
    };
    return a;
  },
  subtextmessage:function(){
    var key = new Date().getTime();
    var a={
      "buttonaddsub[menuName]": $("#submenu .menuname").val(),
      "buttonaddsub[mOrder]": $("#submenu .belongto").val(),
      "buttonaddsub[eventtype]":'click',
      "buttonaddsub[Content]": $("#submenu .textcontent").val(),
      "buttonaddsub[MsgType]": 'text',
      "buttonaddsub[eventKey]": "e"+key,
    };
    return a;
  },
  sublocationmessage:function(){
    var key = new Date().getTime();
    var a={
      "buttonaddsub[menuName]": $("#submenu .menuname").val(),
      "buttonaddsub[mOrder]": $("#submenu .belongto").val(),
      "buttonaddsub[eventtype]":'location_select',
      "buttonaddsub[eventKey]": "e"+key,
    };
    return a;
  },
  editexternalpage:function(){
    var self = this;
    var a={
      "buttonupdate[id]": self.editinfo['id'],
      "buttonupdate[menuName]": $("#editmenu .menuname").val(),
      "buttonupdate[eventtype]": 'view',
      "buttonupdate[eventUrl]": $("#editmenu .viewurl").val(),
    };
    return a;
  },
  editmediamessage: function(){//AAAAAAAAAAA
    var self = this;
    var mid = $("#editmenu .msgnewradio:checked").val();
    if(!mid)
      mid = "";
    var a={
      "buttonupdate[id]": self.editinfo['id'],
      "buttonupdate[menuName]": $("#editmenu .menuname").val(),
      "buttonupdate[eventtype]": 'media_id',
      "buttonupdate[eventmedia_id]": mid,
    };
    return a;
  },
  editpushmessage:function(){
    var self = this;
    var key;
    if(self.editinfo.hasOwnProperty("eventKey") && self.editinfo["eventKey"]){
      key = self.editinfo['eventKey'];
    }else{
      key = "e"+new Date().getTime();
    }
    var a = {
      "buttonupdate[id]": self.editinfo['id'],
      "buttonupdate[menuName]": $("#editmenu .menuname").val(),
      "buttonupdate[eventtype]": 'click',
      "buttonupdate[MsgType]": 'news',
      "buttonupdate[eventKey]": key,
      "buttonupdate[newslist]": self.getnewslist($("#editmenu .pushmessage .newslist")),
    };
    return a;
  },
  edittextmessage:function(){
    var self = this;
    var key;
    if(self.editinfo.hasOwnProperty("eventKey") && self.editinfo["eventKey"]){
      key = self.editinfo['eventKey'];
    }else{
      key = "e"+new Date().getTime();
    }
    var a={
      "buttonupdate[id]": self.editinfo['id'],
      "buttonupdate[menuName]": $("#editmenu .menuname").val(),
      "buttonupdate[eventtype]":'click',
      "buttonupdate[Content]": $("#editmenu .textcontent").val(),
      "buttonupdate[MsgType]": 'text',
      "buttonupdate[eventKey]": key,
    };
    return a;
  },
  editlocationmessage:function(){
    var key = new Date().getTime();
    var self = this;
    var a={
      "buttonupdate[id]": self.editinfo['id'],
      "buttonupdate[menuName]": $("#editmenu .menuname").val(),
      "buttonupdate[eventtype]":'location_select',
      "buttonupdate[eventKey]": "e"+key,
    };
    return a;
  },
  editnone:function(){
    var self = this;
    var a={
      "buttonupdate[id]": self.editinfo['id'],
      "buttonupdate[menuName]": $("#editmenu .menuname").val(),
    };
    return a;
  },
  cleaninput:function(obj){
    $(obj+" input").each(function(){
      $(this).val("");
    });
    $(obj).find(".externalpage").html('');
    $(obj).find(".pushmessage").html('');
    $(obj).find(".textmessage").html('');
  },
  ajaxaddmbutton:function(){
    popup.openloading();
    var self = this;
    var up = menu[self.mbuttonfun]();
    $.ajax({
      type:'post',
      url: '/wechatmenu/addmbutton',
      data: up,
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == '10'){
          menu.cleaninput("#myModal");
          $('#myModal').modal('hide');
          popup.openwarning(data.msg);
          menu.ajaxreload();
          return true;
        }
        popup.openwarning(data.msg);
      },
      error:function(){
        popup.closeloading();
        menu.ajaxreload();
        popup.openwarning('unknow error');
      }
    });
  },
  ajaxaddsubbutton:function(){
    popup.openloading();
    var self = this;
    var up = menu[self.subbuttonfun]();
    $.ajax({
      type:'post',
      url: '/wechatmenu/addsubbutton',
      data: up,
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == '10'){
          menu.cleaninput("#submenu");
          $('#submenu').modal('hide');
          popup.openwarning(data.msg);
          menu.ajaxreload();
          return true;
        }
        popup.openwarning(data.msg);
      },
      error:function(){
        popup.closeloading();
        popup.openwarning('unknow error');
      }
    });
  },
  ajaxreload:function(){
    // window.location.reload();
    // return true;//reload modfid
    $.ajax({
      type:"post",
      url: "/wechatmenu/getmenus",
      dataType:"json",
      success: function(data){
        $(".menu-hierarachy").html(menu.buildtd(data['menus']));
        menu.menumovelisten();//add button listener
        menu.recoverMneus();
      },
      error:function(){
        popup.openwarning('unknow error');
      },
    });
  },
  buildtd:function(data){
    var la = data.length;
    var a = "";
    for(var i=0 ;i<la ;i++){
      a += '<div class="m-slid" menuid="'+data[i]['data']['id']+'">';
      a += '<input id="s'+data[i]['data']['id']+'" type="checkbox" class="d-scbu d-scboxinput" '+((this.menuStatus.hasOwnProperty("s"+data[i]['data']['id']))?(this.menuStatus["s"+data[i]['data']['id']]?"checked='checked'":""):"")+'>';
      a += '<div class="m-slidbar">';
      a += '<div class="m-sonshow m-push-left">';
      if(data[i].hasOwnProperty('son') && data[i]['son'].length){
        a += '<label for="s'+data[i]['data']['id']+'" class="d-scbox">';
        a += '<i class="fa fa-chevron-right"></i>';
        a += '<i class="fa fa-chevron-down"></i>';
        a += '</label>';
      }
      a += '</div>';
      a += '<div class="m-menuinfo m-push-left">';
      a += '<div class="m-menuname m-push-left">'+data[i]['data']['menuName']+'</div>';
      a += '<div class="m-menuevent m-push-left">'+((data[i]['data']['eventtype'])?data[i]['data']['eventtype']:'')+'</div>';
      a += '</div>';
      a += '<div class="m-menumove m-push-right">';
      a += '<i class="fa fa-arrows fa-lg"></i>';
      a += '</div>';
      a += '<div class="m-menuchange  m-push-right">';
      a += '<i class="fa fa-trash-o fa-lg"></i>';
      a += '<i class="fa fa-edit fa-lg"></i>';
      a += '</div>';
      a += '</div>';
      a += '<div class="m-slidbox">';
      if(data[i].hasOwnProperty('son') && data[i]['son'].length){
        a += menu.buildtd(data[i]['son']);
      }
      a += '</div>';
      a += '</div>';
    }
    return a;
  },
  delbutton: function(){
    obj = this.delobj;
    var id = obj.parent().parent().parent().attr('menuid');
    popup.openloading();
    $.ajax({
      type:'post',
      url: '/wechatmenu/deletebutton',
      dataType:'json',
      data: {
          "buttondel[id]": id,
        },
      success: function(data){
        popup.closeloading();
        if(data.code == '10'){
          popup.closecomfirm();
          menu.ajaxreload();
          popup.openwarning(data.msg);
          return true;
        }
        popup.openwarning(data.msg);
      },
      error:function(){
        popup.closeloading();
        popup.openwarning('unknow error');
      }
    });
  },
  publishmenu:function(){
    popup.openloading();
    $.ajax({
      url:"/wechatmenu/createmenu",
      type:"post",
      dataType:'json',
      success:function(data){
        popup.closeloading();
        if(data.code == '10'){
          popup.openwarning(data.msg);
          return true;
        }
        popup.openwarning(data.msg);
      },
      error:function(){
        popup.closeloading();
        popup.openwarning('unknow errors');
      }
    });
  },
  ajaxgetmbuttom:function(){
    popup.openloading();
    $.ajax({
      url:"/wechatmenu/getmmenu",
      type:"post",
      dataType:'json',
      success:function(data){
        popup.closeloading();
        if(data.code == '10'){
          $("#submenu .belongto").html(htmlconetnt.belongtohtml(data.menus));
          $('#submenu').modal('show');
          return true;
        }
        popup.openwarning(data.msg);
      },
      error:function(){
        popup.closeloading();
        popup.openwarning('unknow errors');
      }
    });
  },
  ajaxgetbuttoninfo:function(id){
    popup.openloading();
    $.ajax({
      url:"/wechatmenu/getbuttoninfo",
      type:"post",
      dataType:'json',
      data:{
        "buttoninfo[id]": id,
      },
      success:function(data){
        popup.closeloading();
        if(data.code == '10'){
          menu.editbutton(data['info']);
          $('#editmenu').modal('show');
          return true;
        }
        popup.openwarning(data.msg);
      },
      error:function(){
        popup.closeloading();
        popup.openwarning('unknow errors');
      }
    });
  },
  editbutton:function(data){
    var self = this;
    self.editinfo = data;
    $("#editmenu .buttontype .active").removeClass("active");
    $('#editmenu .menuname').val(data['menuName']);
    $("#editmenu .menushow").removeClass("menushow");
    self.editbuttonfun = "editnone";
    if(self.edithidden){
      $("#editmenu .buttontype").hide();
      return false;
    }
    if(data['eventtype'] == 'view'){
      $("#editmenu .externalpage").addClass("menushow");
      $("#editmenu .buttontype .btn").eq(0).addClass("active");
      $("#editmenu .externalpage").html(htmlconetnt.aview(data['eventUrl']));
      self.editbuttonfun = "editexternalpage";
      return true;
    }
    if(data['eventtype'] == 'media_id'){//AAAAAAAAAAA
      $("#editmenu .mediamessage").addClass("menushow");
      $("#editmenu .buttontype [MsgType='media_id']").addClass("active");
      var info = data.hasOwnProperty("eventmedia_info")?data['eventmedia_info']:[];
      $("#editmenu .mediamessage").html(htmlconetnt.amediamessage(info));
      self.editbuttonfun = "editmediamessage";
      return true;
    }
    if(data['eventtype'] == 'click'){
      if(data['buttonevent'].hasOwnProperty('newslist')){
        $("#editmenu .pushmessage").addClass("menushow");
        $("#editmenu .buttontype .btn").eq(1).addClass("active");
        $("#editmenu .pushmessage").html(htmlconetnt.apushmessage(data['buttonevent']['newslist']));
        self.editbuttonfun = "editpushmessage";
        return true;
      }
      if(data['buttonevent']['MsgType'] == 'text'){
        $("#editmenu .textmessage").addClass("menushow");
        $("#editmenu .buttontype .btn").eq(2).addClass("active");
        $("#editmenu .textmessage").html(htmlconetnt.atextmessage(data['buttonevent']['Content']));
        self.editbuttonfun = "edittextmessage";
        return true;
      }
    }
    if(data['eventtype'] == 'location_select'){
      $("#editmenu .locationmessage").addClass("menushow");
      $("#editmenu .buttontype .btn").eq(3).addClass("active");
      self.editbuttonfun = "editlocationmessage";
      return true;
    }
  },
  ajaxupdatebutton:function(){
    popup.openloading();
    var self = this;
    var up = menu[self.editbuttonfun]();
    $.ajax({
      url: "/wechatmenu/updatebutton",
      data: up,
      type:"post",
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == '10'){
          menu.ajaxreload();
          menu.cleaninput("#editmenu");
          popup.openwarning(data.msg);
          $('#editmenu').modal('hide');
          return true;
        }
        popup.openwarning(data.msg);
      },
      error:function(){
        popup.closeloading();
        popup.openwarning('unknow errors');
      }
    });
  },
  historyindex: null,
  removehistorystyle: function(id){
    if(this.historyindex != id && this.historyindex !== null)
      menu.onmoveingBox.parent().parent().children(".m-slid").eq(this.historyindex).removeAttr("style");
    this.historyindex = id;
  },
  buildNewList: function(){
    var btop = parseInt(menu.onmoveingBox.parent().css("top"));
    var fontIndex = parseInt(btop/menu.boxheight) + ((parseInt(btop%menu.boxheight) > parseInt(menu.boxheight/2))?0:-1);
    var father = menu.onmoveingBox.parent().parent();
    if(fontIndex >= menu.boxindex){
      fontIndex = fontIndex + 1;
    }
    if(fontIndex != -1){
      father.children(".m-slid").eq(fontIndex).after(menu.onmoveingBox.parent());
      if(fontIndex != menu.boxindex-1)
        menu.buildNewListJson();
    }else{
      father.prepend(menu.onmoveingBox.parent());
      if(menu.boxindex != 0)
        menu.buildNewListJson();
    }
  },
  buildNewListJson: function(){
    var list = [];
    menu.onmoveingBox.parent().parent().children(".m-slid").each(function(){
      list.push($(this).attr("menuid"));
    });
    var menuid = menu.onmoveingBox.parent().parent().parent().attr("menuid");
    menuid = (menuid)?menuid:0;
    menu.NewList[menuid] = list;
    $(".menu-changeranking").show();
    menu.onmoveingBox.addClass("barchange");
    console.log(menu.NewList);
  },
  menuStatus:{},
  saveMenustatus: function(mid){
    this.menuStatus[mid] = ($("#"+mid).prop("checked"))?false:true;
  },
  recoverMneus: function(){
    for(var i in this.menuStatus){
      $("#"+i).prop("checked", this.menuStatus[i]);
    }
  },
  NewList: [],
  submitNewlist: function(){
    popup.openloading();
    var self = this;
    $.ajax({
      url: "/wechatmenu/newmenuranking",
      data: {
        "newmenuranking[menulist]": self.NewList,
      },
      type:"post",
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == '10'){
          menu.ajaxreload();
          $(".menu-changeranking").hide();
          return true;
        }
        popup.openwarning(data.msg);
      },
      error:function(){
        popup.closeloading();
        popup.openwarning('unknow errors');
      }
    });
  },
  menumousemoveM: function(e){
    e.preventDefault();
    var gT = e.clientY-mouseinfo.mouseT;
    var btop = parseInt(gT + menu.slidboxT);
    var gapindex = parseInt(gT/menu.boxheight) + parseInt((gT > 0)?1:(-1)) + menu.boxindex;
    // console.log(gapindex + menu.boxindex);return false;
    var rem = Math.abs(parseInt(gT%menu.boxheight));
    var margint = (gT > 0)?(menu.boxheight-rem):rem;
    var marginb = menu.boxheight-margint;
    if(btop <= 0){
      margint = menu.boxheight;
      marginb = 0;
      btop = 0;
      if( menu.boxindex == 0){
        gapindex = 1;
      }else{
        gapindex = 0;
      }
    }
    if(btop >= parseInt(parseInt(menu.slidboxLength-1)*menu.boxheight)){
      btop = parseInt(menu.slidboxLength-1)*menu.boxheight;
      margint = 0;
      marginb = menu.boxheight;
      if( menu.boxindex == parseInt(menu.slidboxLength-1)){
        gapindex = parseInt(menu.slidboxLength-2);
      }else{
        gapindex = parseInt(menu.slidboxLength-1);
      }
    }
    menu.onmoveingBox.parent().css({
      position: "absolute",
      top: btop,
      width: menu.boxwidth,
      "z-index": 100,
      opacity: 0.8
    });
    if(gapindex != menu.boxindex){
      menu.onmoveingBox.parent().parent().children(".m-slid").eq(gapindex).css({
        "margin-top": margint + "px",
        "margin-bottom": marginb +"px"
      });
    }
    menu.removehistorystyle(gapindex);
  },
  slidboxT: null,
  slidboxLength: null,
  boxheight: null,
  boxindex: null,
  boxwidth: null,
  initSlidbox: function(){
    this.onmoveingBox.parent().parent().find(".d-scboxinput").each(function(){
      $(this).prop("checked",false);
    });
    this.slidboxT = this.onmoveingBox["0"].offsetTop;
    this.boxheight = parseInt(this.onmoveingBox.parent().css("height"));
    this.boxindex = this.onmoveingBox.parent().index();
    this.boxwidth = parseInt(this.onmoveingBox.parent().css("width"));
    this.slidboxLength = this.onmoveingBox.parent().parent().children(".m-slid").length;
  },
  onmoveingBox: null,
  removeBoxListener: function(){
    var self = this;
    menu.removehistorystyle(null);
    menu.buildNewList();
    document.removeEventListener('mousemove', menu.menumousemoveM);
    menu.onmoveingBox.parent().removeAttr("style");
    menu.recoverMneus();
    document.removeEventListener('mouseup', menu.removeBoxListener);
  },
  menumovelisten: function(){
    var self = this;
    $(".menu-hierarachy .m-slidbar .m-menumove").each(function(){
      $(this)['0'].addEventListener('mousedown', function(e){
        mouseinfo.initMouse(e);
        menu.onmoveingBox = $(this).parent();
        menu.initSlidbox();
        document.addEventListener('mousemove', menu.menumousemoveM);
        document.addEventListener('mouseup', menu.removeBoxListener);
      });
    });
  },
  onload: function(){
    var self = this;
    $("#myModal .buttontype .btn").click(function(){//add main menu 's submenu
      $("#myModal .buttontype .active").removeClass("active");
      $(this).addClass("active");
      self.showfeedback($(this));
    });
    $("#menufun>.addmainmenu").click(function(){//add main menu
      if(!self.mbuttonfun)
        self.mbuttonfun = "mnone";
      $('#myModal').modal('show');
    });
    $("#menufun>.addsubmenu").click(function(){//add submenu ajax
      if(!self.subbuttonfun)
        self.subbuttonfun = "subnone";
      self.ajaxgetmbuttom();
    });
    $("#submenu .buttontype .btn").click(function(){//add main menu 's submenu
      $("#submenu .buttontype .active").removeClass("active");
      $(this).addClass("active");
      self.showfeedback2($(this));
    });
    $("#editmenu .buttontype .btn").click(function(){//edit menu 's submenu
      $("#editmenu .buttontype .active").removeClass("active");
      $(this).addClass("active");
      self.showfeedback3($(this));
    });
    $("#myModal .addmmenusubmit").click(function(){
      self.ajaxaddmbutton();
    });
    $("#menufun>.publish").click(function(){
      self.publishmenu();
    });
    $("#myModal").on("click",".fa-minus-square", function(){
      $(this).parent().remove();
    });
    $("#myModal").on("click",".fa-plus-square", function(){
      var a = htmlconetnt.addnewshtml();
      $("#myModal .pushmessage .mytipsboxcs").remove();
      $(this).after(a);
      $(this).remove();
      if($("#myModal .pushmessage .fa-minus-square").length >= 10){
        $("#myModal .pushmessage .fa-plus-square").remove();
        $("#myModal .pushmessage .mytipsboxcs").remove();
      }
    });
    $("#myModal").on("change", ".newsfile", function(){
      fileupload.sendfiles($(this)[0].files[0], $(this));
    });
    $("#myModal").on("click",".fa-times",function(){
      fileupload.replaceimage($(this));
    });
    $("#submenu").on("click",".fa-minus-square", function(){
      $(this).parent().remove();
    });
    $("#submenu").on("click",".fa-plus-square", function(){
      var a = htmlconetnt.addnewshtml();
      $("#submenu .pushmessage .mytipsboxcs").remove();
      $(this).after(a);
      $(this).remove();
      if($("#submenu .pushmessage .fa-minus-square").length >= 10){
        $("#submenu .pushmessage .fa-plus-square").remove();
        $("#submenu .pushmessage .mytipsboxcs").remove();
      }
    });
    $("#submenu").on("change", ".newsfile", function(){
      fileupload.sendfiles($(this)[0].files[0], $(this));
    });
    $("#submenu").on("click",".fa-times",function(){
      fileupload.replaceimage($(this));
    });
    $("#editmenu").on("click",".fa-minus-square", function(){
      $(this).parent().remove();
    });
    $("#editmenu").on("click",".fa-plus-square", function(){
      var a = htmlconetnt.addnewshtml();
      $("#editmenu .pushmessage .mytipsboxcs").remove();
      $(this).after(a);
      $(this).remove();
      if($("#editmenu .pushmessage .fa-minus-square").length >= 10){
        $("#editmenu .pushmessage .fa-plus-square").remove();
        $("#editmenu .pushmessage .mytipsboxcs").remove();
      }
    });
    $("#editmenu").on("change", ".newsfile", function(){
      fileupload.sendfiles($(this)[0].files[0], $(this));
    });
    $("#editmenu").on("click",".fa-times",function(){
      fileupload.replaceimage($(this));
    });
    $("#addsubmenusubmit").click(function(){
      self.ajaxaddsubbutton();
    });
    $(".menu-hierarachy").on("click", ".m-menuchange .fa-edit", function(){
      var parent = $(this).parent().parent().parent();
      var id = parent.attr("menuid");
      self.edithidden = 0;
      $("#editmenu .buttontype").removeAttr('style');
      if(parent.children('.m-slidbox').eq(0).children('div').length){
        self.edithidden = 1;
      }
      self.ajaxgetbuttoninfo(id);
    });
    $(".menu-hierarachy").on("click", ".m-menuchange .fa-trash-o", function(){
      self.delobj = $(this);
      popup.opencomfirm("delete this menu ???","menu.delbutton()");
    });
    $("#savechange").click(function(){
      self.ajaxupdatebutton();
    });
    //menuxmove
    $(".menu-hierarachy").on("mouseup",".m-sonshow", function(){
      var mid = $(this).children(".d-scbox").attr("for");
      self.saveMenustatus(mid);
    });
    $("#rangkingsave").click(function(){
      self.submitNewlist();
    });
  },
}

var keyword = {
  editinfo:null,
  addfun:null,
  editfun:null,
  cleanadd: function(){
    $("#addtagdiv .inputtagname").val('');
    var a = '<div class="form-group">';
        a += '<label>Key Word:</label>';
        a += '<i class="fa fa-minus-circle"></i>';
        a += '<input class="form-control inputkeyword" placeholder="Enter Key Word" style="width:80%">';
        a += '</div>';
        a += '<i class="fa fa-plus-circle"></i>';
    $("#addtagdiv .taglist").html(a);
    $("#addtagdiv .pushmessage").html(htmlconetnt['pushmessage']());
    $("#addtagdiv .textmessage").html(htmlconetnt['textmessage']());
  },
  gotolist:function(){
    $("#tagnav .active").removeClass("active");
    $("#tagnav li").eq(0).addClass("active");
    $("#tagmanage .navshow").removeClass("navshow");
    $("#taglist").addClass("navshow");
  },
  showaddedit: function(obj){
    var self = this;
    var action = obj.attr('action');
    if($("#addtagdiv ."+action+" div").length == 0)
      $("#addtagdiv ."+action).html(htmlconetnt[action]());
    $("#addtagdiv .menushow").removeClass("menushow");
    $("#addtagdiv ."+action).addClass("menushow");
    self.addfun = "a"+action;
  },
  showaddedit2: function(obj){
    var self = this;
    var action = obj.attr('action');
    if($("#tagkeyslist ."+action+" div").length == 0)
      $("#tagkeyslist ."+action).html(htmlconetnt[action]());
    $("#tagkeyslist .menushow").removeClass("menushow");
    $("#tagkeyslist ."+action).addClass("menushow");
    self.editfun = "e"+action;
  },
  apushmessage:function(){
    var self = this;
    var a = {
      "keywordadd[Tagname]": $("#addtagdiv .inputtagname").val(),
      "keywordadd[keywords]": self.getkeywords($("#addtagdiv .taglist .inputkeyword")),
      "keywordadd[MsgType]": 'news',
      "keywordadd[newslist]": menu.getnewslist($("#addtagdiv .pushmessage .newslist")),
    };
    return a;
  },
  atextmessage:function(){
    var self = this;
    var a={
      "keywordadd[Tagname]": $("#addtagdiv .inputtagname").val(),
      "keywordadd[keywords]": self.getkeywords($("#addtagdiv .taglist .inputkeyword")),
      "keywordadd[MsgType]": 'text',
      "keywordadd[Content]": $("#addtagdiv .textcontent").val(),
    };
    return a;
  },
  epushmessage:function(){
    var self = this;
    var a = {
      "keywordupdate[menuId]": self.editinfo['menuId'],
      "keywordupdate[Tagname]": $("#tagkeyslist .inputtagname").val(),
      "keywordupdate[keywords]": self.getkeywords($("#tagkeyslist .taglist .inputkeyword")),
      "keywordupdate[MsgType]": 'news',
      "keywordupdate[newslist]": menu.getnewslist($("#tagkeyslist .pushmessage .newslist")),
    };
    return a;
  },
  etextmessage:function(){
    var self = this;
    var a={
      "keywordupdate[menuId]": self.editinfo['menuId'],
      "keywordupdate[Tagname]": $("#tagkeyslist .inputtagname").val(),
      "keywordupdate[keywords]": self.getkeywords($("#tagkeyslist .taglist .inputkeyword")),
      "keywordupdate[MsgType]": 'text',
      "keywordupdate[Content]": $("#tagkeyslist .textcontent").val(),
    };
    return a;
  },
  getkeywords: function(obj){
    var keys = [];
    obj.each(function(){
      keys.push($(this).val());
    });
    return JSON.stringify(keys);
  },
  ajaxtaglist:function(){
    var self = this;
    popup.openloading();
    $.ajax({
      url:"/wechat/getkeywordlist",
      type:"post",
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          keyword.buildtbody(data["list"]);
        }
        if(data.code == "9")
         keyword.buildtbody({});
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxdeltag: function(menuId){
    var self = this;
    popup.openloading();
    $.ajax({
      url:"/wechat/keyworddel",
      type:"post",
      dataType:'json',
      data:{
        'keyworddel[menuId]':menuId,
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          keyword.ajaxtaglist();
          return true
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  taglisttable: null,
  buildtbody: function(data){
    if(this.taglisttable)
     this.taglisttable.destroy();
    $('#taglisttable tbody').empty();
    this.taglisttable = $('#taglisttable').DataTable( {
      "data": data,
      "columns": [
        { "data": "Tagname" },
        {
          "class": "t-center",
          "data": null,
          "defaultContent": '<i class="fa fa-edit fa-lg"></i>'
        },
        {
          "class": "t-center",
          "data": null,
          "defaultContent": '<i class="fa fa-trash-o fa-lg"></i>'
        }
      ],
      "rowCallback": function( row, data ) {
          $(row).attr('menuid',data.menuId);
      }
    } );
  },
  ajaxaddtag: function(){
    var self = this;
    popup.openloading();
    var up = keyword[self.addfun]();
    $.ajax({
      url:"/wechat/keywordadd",
      type:"post",
      dataType:'json',
      data: up,
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          keyword.ajaxtaglist();
          keyword.cleanadd();
          keyword.gotolist();
        }
        popup.openwarning(data.msg);
        return true;
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxtaginfo:function(menuId){
    var self = this;
    popup.openloading();
    $.ajax({
      url:"/wechat/getkeywordinfo",
      type:"post",
      dataType:'json',
      data:{
        'keywordinfo[menuId]':menuId,
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          keyword.buildtaginfo(data.info);
          $("#tagmanagepanel .navshow").removeClass("navshow");
          $("#tagkeyslist").addClass("navshow");
          return true
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  buildtaginfo: function(data){
    var self = this;
    self.editinfo = data;
    $("#tagkeyslist .taglist").html(htmlconetnt.showtagkeyword(data.getContent));
    $("#tagkeyslist .inputtagname").val(data.Tagname);
    $("#tagkeyslist .buttontype .active").removeClass("active");
    $("#tagkeyslist .menushow").removeClass("menushow");
    if(data.MsgType == "text"){
      $("#tagkeyslist .buttontype .btn").eq(0).addClass("active");
      $("#tagkeyslist .textmessage").addClass("menushow");
      self.editfun = "etextmessage";
      $("#tagkeyslist .textmessage").html(htmlconetnt.atextmessage(data.Content));
      return true;
    }
    if(data.MsgType == "news"){
      $("#tagkeyslist .buttontype .btn").eq(1).addClass("active");
      $("#tagkeyslist .pushmessage").addClass("menushow");
      self.editfun = "epushmessage";
      $("#tagkeyslist .pushmessage").html(htmlconetnt.apushmessage(data.newslist));
      return true;
    }
  },
  keywordupdate:function(){
    var self = this;
    popup.openloading();
    var up = keyword[self.editfun]();
    $.ajax({
      url:"/wechat/keywordupdate",
      type:"post",
      dataType:'json',
      data: up,
      success:function(data){
        popup.closeloading();
        if(data.code == "10"){
          keyword.ajaxtaglist();
          keyword.gotolist();
          popup.openwarning(data.msg);
          return true;
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  onload: function(){
    var self = this;
    $("#addtagdiv .buttontype .btn").click(function(){//add event 's submenu
      $("#addtagdiv .buttontype .active").removeClass("active");
      $(this).addClass("active");
      self.showaddedit($(this));
    });
    $("#tagnav .message").click(function(){
      $("#tagmanage .active").removeClass("active");
      $(this).parent().addClass("active");
      $("#tagmanage .navshow").removeClass("navshow");
      var active = $(this).attr("active");
      $("#"+active).addClass("navshow");
    });
    $("#addtagdiv .taglist").on("click",".fa-plus-circle", function(){
      $(this).before(htmlconetnt.tagkeyword);
    });
    $("#addtagdiv .taglist").on("click",".fa-minus-circle", function(){
      $(this).parent().remove();
    });
//add tag message
    $("#addtagdiv").on("click",".fa-minus-square", function(){
      $(this).parent().remove();
    });
    $("#addtagdiv").on("click",".fa-plus-square", function(){
      var a = htmlconetnt.addnewshtml();
      $("#addtagdiv .pushmessage .mytipsboxcs").remove();
      $(this).after(a);
      $(this).remove();
      if($("#addtagdiv .pushmessage .fa-minus-square").length >= 10){
        $("#addtagdiv .pushmessage .fa-plus-square").remove();
        $("#addtagdiv .pushmessage .mytipsboxcs").remove();
      }
    });
    $("#addtagdiv").on("change", ".newsfile", function(){
      fileupload.sendfiles($(this)[0].files[0], $(this));
    });
    $("#addtagdiv").on("click",".fa-times",function(){
      fileupload.replaceimage($(this));
    });
//add tag message end
// edit tag
    $("#tagkeyslist").on("click",".fa-minus-square", function(){
      $(this).parent().remove();
    });
    $("#tagkeyslist").on("click",".fa-plus-square", function(){
      var a = htmlconetnt.addnewshtml();
      $("#tagkeyslist .pushmessage .mytipsboxcs").remove();
      $(this).after(a);
      $(this).remove();
      if($("#tagkeyslist .pushmessage .fa-minus-square").length >= 10){
        $("#tagkeyslist .pushmessage .fa-plus-square").remove();
        $("#tagkeyslist .pushmessage .mytipsboxcs").remove();
      }
    });
    $("#tagkeyslist").on("change", ".newsfile", function(){
      fileupload.sendfiles($(this)[0].files[0], $(this));
    });
    $("#tagkeyslist").on("click",".fa-times",function(){
      fileupload.replaceimage($(this));
    });
    $("#tagkeyslist .taglist").on("click",".fa-plus-circle", function(){
      $(this).before(htmlconetnt.tagkeyword);
    });
    $("#tagkeyslist .taglist").on("click",".fa-minus-circle", function(){
      $(this).parent().remove();
    });
    $("#tagkeyslist .buttontype .btn").click(function(){//add event 's submenu
      $("#tagkeyslist .buttontype .active").removeClass("active");
      $(this).addClass("active");
      self.showaddedit2($(this));
    });
// edit tag end
    $("#addtagsubmit").click(function(){
      self.ajaxaddtag();
    });
    $("#taglist").on("click", "tbody .fa-trash-o", function(){
      var menuId = $(this).parent().parent().attr("menuid");
      self.ajaxdeltag(menuId);
    });
    $("#tagkeyslist .panel-heading .fa-mail-reply").click(function(){
      $("#tagmanagepanel .navshow").removeClass("navshow");
      $("#taglist").addClass("navshow");
    });
    $("#taglist").on("click", "tbody .fa-edit", function(){
      var menuId = $(this).parent().parent().attr("menuid");
      self.ajaxtaginfo(menuId);
    });
    $("#tagchangesubmit").click(function(){
      self.keywordupdate();
    });
  }
}

var autoreplay = {
  welcomefun:null,
  defaultfun:null,
  navactive:null,
  getEvent:null,
  showwelcome: function(obj){
    var self = this;
    var action = obj.attr('action');
    if($("#welcomemessage ."+action+" div").length == 0)
      $("#welcomemessage ."+action).html(htmlconetnt[action]());
    $("#welcomemessage .menushow").removeClass("menushow");
    $("#welcomemessage ."+action).addClass("menushow");
    self.welcomefun = "w"+action;
  },
  showdefault:function(obj){
    var self = this;
    var action = obj.attr('action');
    if($("#defaultmessage ."+action+" div").length == 0)
      $("#defaultmessage ."+action).html(htmlconetnt[action]());
    $("#defaultmessage .menushow").removeClass("menushow");
    $("#defaultmessage ."+action).addClass("menushow");
    self.defaultfun = "d"+action;
  },
  wpushmessage:function(){
    var self = this;
    var a = {
      "autoreply[getMsgType]": "event",
      "autoreply[getEvent]": "subscribe",
      "autoreply[MsgType]": 'news',
      "autoreply[newslist]": menu.getnewslist($("#welcomemessage .pushmessage .newslist")),
    };
    return a;
  },
  wtextmessage:function(){
    var self = this;
    var a={
      "autoreply[getMsgType]": "event",
      "autoreply[getEvent]": "subscribe",
      "autoreply[MsgType]": 'text',
      "autoreply[Content]": $("#welcomemessage .textcontent").val(),
    };
    return a;
  },
  dpushmessage:function(){
    var self = this;
    var a = {
      "autoreply[getMsgType]": "event",
      "autoreply[getEvent]": "defaultback",
      "autoreply[MsgType]": 'news',
      "autoreply[newslist]": menu.getnewslist($("#defaultmessage .pushmessage .newslist")),
    };
    return a;
  },
  dtextmessage:function(){
    var self = this;
    var a={
      "autoreply[getMsgType]": "event",
      "autoreply[getEvent]": "defaultback",
      "autoreply[MsgType]": 'text',
      "autoreply[Content]": $("#defaultmessage .textcontent").val(),
    };
    return a;
  },
  ajaxwecomeup:function(){
    popup.openloading();
    var self = this;
    var up = autoreplay[self.welcomefun]();
    $.ajax({
      url: "/wechat/autoreply",
      type:"post",
      dataType:'json',
      data: up,
      success: function(data){
        popup.closeloading();
        if(data.code == '10'){
          popup.openwarning(data.msg);
          return true;
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxdefaultup:function(){
    popup.openloading();
    var self = this;
    var up = autoreplay[self.defaultfun]();
    $.ajax({
      url: "/wechat/autoreply",
      type:"post",
      dataType:'json',
      data: up,
      success: function(data){
        popup.closeloading();
        if(data.code == '10'){
          popup.openwarning(data.msg);
          return true;
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxautoreply:function(getEvent){
    popup.openloading();
    var self = this;
    $.ajax({
      url: "/wechat/autoreplyinfo",
      type:"post",
      dataType:'json',
      data: {
        "autoreplyload[getEvent]": getEvent,
      },
      success: function(data){
        $("#"+autoreplay.navactive).addClass("navshow");
        popup.closeloading();
        if(data.code == '10'){
          autoreplay.buildMsg(data.info);
          return true;
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  buildMsg: function(data){
    var self = this;
    if(data.MsgType == "text"){
      if(self.navactive == "defaultmessage")
        autoreplay.defaultfun = "dtextmessage";
      if(self.navactive == "welcomemessage")
        autoreplay.welcomefun = "wtextmessage";
      $("#"+self.navactive+" .buttontype .btn").eq(0).addClass("active");
      $("#"+self.navactive+" .textmessage").addClass("menushow");
      $("#"+self.navactive+" .textmessage").html(htmlconetnt.atextmessage(data.MsgData.Content));
      return true;
    }
    if(data.MsgType == "news"){
      if(self.navactive = "defaultmessage")
        autoreplay.defaultfun = "dpushmessage";
      if(self.navactive == "welcomemessage")
        autoreplay.welcomefun = "wpushmessage";
      $("#"+self.navactive+" .buttontype .btn").eq(1).addClass("active");
      $("#"+self.navactive+" .pushmessage").addClass("menushow");
      $("#"+self.navactive+" .pushmessage").html(htmlconetnt.apushmessage(data.MsgData.Articles));
      return true;
    }
  },
  ajaxreplydel:function(getEvent){
    popup.openloading();
    var self = this;
    $.ajax({
      url: "/wechat/autoreplydel",
      type:"post",
      dataType:'json',
      data: {
        "autoreplydel[getEvent]": getEvent,
        "autoreplydel[getMsgType]": 'event',
      },
      success: function(data){
        popup.closeloading();
        if(data.code == '10'){
          autoreplay.cleanMsg($("#"+autoreplay.navactive));
          autoreplay.ajaxautoreply(autoreplay.getEvent);
          return true;
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  cleanMsg: function(obj){
    obj.find(".pushmessage").html('');
    obj.find(".textmessage").html('');
  },
  onload: function(){
    var self = this;
    $("#autoreplaynav .message").click(function(){
      $("#autoreplaynav .active").removeClass("active");
      $(this).parent().addClass("active");
      $("#autoreload .navshow").removeClass("navshow");
      var active = $(this).attr("active");
      self.getEvent = $(this).attr("getEvent");
      self.navactive = active;
      self.cleanMsg($("#"+active));
      self.ajaxautoreply(self.getEvent);
    });
    $("#welcomemessage .buttontype>.btn").click(function(){
      $("#welcomemessage .buttontype .active").removeClass("active");
      $(this).addClass("active");
      self.showwelcome($(this));
    });
    $("#defaultmessage .buttontype>.btn").click(function(){
      $("#defaultmessage .buttontype .active").removeClass("active");
      $(this).addClass("active");
      self.showdefault($(this));
    });
    $("#welcomemessage .savebutton").click(function(){
      self.ajaxwecomeup();
    });
    $("#defaultmessage .savebutton").click(function(){
      self.ajaxdefaultup();
    });
    $("#welcomemessage .welcomedel").click(function(){
      self.ajaxreplydel('subscribe');
    });
    $("#defaultmessage .defaultdel").click(function(){
      self.ajaxreplydel('defaultback');
    });
// welcome msg
    $("#welcomemessage").on("click",".fa-minus-square", function(){
      $(this).parent().remove();
    });
    $("#welcomemessage").on("click",".fa-plus-square", function(){
      var a = htmlconetnt.addnewshtml();
      $("#welcomemessage .pushmessage .mytipsboxcs").remove();
      $(this).after(a);
      $(this).remove();
      if($("#welcomemessage .pushmessage .fa-minus-square").length >= 10){
        $("#welcomemessage .pushmessage .fa-plus-square").remove();
        $("#welcomemessage .pushmessage .mytipsboxcs").remove();
      }
    });
    $("#welcomemessage").on("change", ".newsfile", function(){
      fileupload.sendfiles($(this)[0].files[0], $(this));
    });
    $("#welcomemessage").on("click",".fa-times",function(){
      fileupload.replaceimage($(this));
    });
// welcome msg end
// default msg
    $("#defaultmessage").on("click",".fa-minus-square", function(){
      $(this).parent().remove();
    });
    $("#defaultmessage").on("click",".fa-plus-square", function(){
      var a = htmlconetnt.addnewshtml();
      $("#defaultmessage .pushmessage .mytipsboxcs").remove();
      $(this).after(a);
      $(this).remove();
      if($("#defaultmessage .pushmessage .fa-minus-square").length >= 10){
        $("#defaultmessage .pushmessage .fa-plus-square").remove();
        $("#defaultmessage .pushmessage .mytipsboxcs").remove();
      }
    });
    $("#defaultmessage").on("change", ".newsfile", function(){
      fileupload.sendfiles($(this)[0].files[0], $(this));
    });
    $("#defaultmessage").on("click",".fa-times",function(){
      fileupload.replaceimage($(this));
    });
// default msg end
  }
}

var preference = {
  editinfo:null,
  permiss: [],
  permissid: null,
  ajaxchangpwd:function(){
    var test = [
      [$("#changepwd .oldpassword"), "tnonull", "the oldpassword is empty"],
      [$("#changepwd .newpassword"), "tnonull", "the newpassword is empty"],
      [$("#changepwd .newpassword2"), "tnonull", "the repeat password is empty"],
    ];
    if(!formstr.tallobj(test))
      return false;
    if($("#changepwd .newpassword").val() !== $("#changepwd .newpassword2").val()){
      popup.openwarning('the repeat password error');
      return false;
    }
    popup.openloading();
    $.ajax({
      url: "/user/changepwd",
      type:"post",
      dataType:'json',
      data:{
        "changepwd[oldpassword]": $("#changepwd .oldpassword").val(),
        "changepwd[newpassword]": $("#changepwd .newpassword2").val(),
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          preference.cleanpsw();
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxadduser: function(){
    var test = [
      [$("#adduserbox .username"), "tnonull", "the username is empty"],
      [$("#adduserbox .newpassword"), "tnonull", "the password is empty"],
      [$("#adduserbox .newpassword2"), "tnonull", "the repeat password is empty"],
    ];
    if(!formstr.tallobj(test))
      return false;
    if($("#adduserbox .newpassword").val() !== $("#adduserbox .newpassword2").val()){
      popup.openwarning('the repeat password error');
      return false;
    }
    popup.openloading();
    $.ajax({
      url: "/user/creatadmin",
      type:"post",
      dataType:'json',
      data:{
        "adminadd[username]": $("#adduserbox .username").val(),
        "adminadd[password]": $("#adduserbox .newpassword2").val(),
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          $("#adduserbox").modal('hide');
          preference.ajaxgetadmins();
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxdeluser: function(userid){
    popup.openloading();
    $.ajax({
      url: "/user/userdel",
      type:"post",
      dataType:'json',
      data:{
        "admindel[id]": userid,
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          preference.ajaxgetadmins();
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxgetadmins:function(){
    window.location.reload();
    return true;//reload modfid
    popup.openloading();
    $.ajax({
      url: "/user/getadmins",
      type:"post",
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          $("#usertables tbody").html(preference.buildtbody(data.list));
          return true;
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  getadmininfo:function(userid){
    popup.openloading();
    $.ajax({
      url: "/user/getadminerinfo",
      type:"post",
      dataType:'json',
      data:{
        "admininfo[id]": userid,
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          preference.editinfo = data.info;
          $("#edituserbox .adminname").text(data.info.username);
          $("#edituserbox").modal('show');
          return true;
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  adminchangepwd: function(){
    var test = [
      [$("#edituserbox .newpassword"), "tnonull", "the password is empty"],
      [$("#edituserbox .newpassword2"), "tnonull", "the repeat password is empty"],
    ];
    if(!formstr.tallobj(test))
      return false;
    if($("#edituserbox .newpassword").val() !== $("#edituserbox .newpassword2").val()){
      popup.openwarning('the repeat password error');
      return false;
    }
    popup.openloading();
    $.ajax({
      url: "/user/admincpw",
      type:"post",
      dataType:'json',
      data:{
        "admincpw[id]": preference.editinfo["id"],
        "admincpw[newpassword]": $("#edituserbox .newpassword2").val(),
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          $("#edituserbox").modal('hide');
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  buildtbody: function(data){
    var la = data.length;
    var a="";
    for(var i="0"; i<la; i++){
      a += '<tr class="odd gradeX" userid="'+data[i]['id']+'">';
      a += '<td>'+data[i]['username']+'</td>';
      a += '<td>'+data[i]['latestTime']+'</td>';
      a += '<td class="center"><i class="fa fa-edit fa-lg"></i></td>';
      a += '<td class="center"><i class="fa fa-trash-o fa-lg"></i></td>';
      a += '</tr>';
    }
    return a;
  },
  cleanpsw:function(){
    $("#changepwd .oldpassword").val('');
    $("#changepwd .newpassword2").val('');
    $("#changepwd .newpassword").val('');
  },
  cleanadduser: function(){
    $("#adduserbox .username").val('');
    $("#adduserbox .newpassword2").val('');
    $("#adduserbox .newpassword").val('');
  },
  cleanadmincpw: function(){
    $("#edituserbox .newpassword2").val('');
    $("#edituserbox .newpassword").val('');
  },
  buildpertable: function(data){
    var a = "";
    var la = 0;
    for(var x in data.allpers){
      var b = data.pers.indexOf(x);
      a += "<tr>";
      a += "<td>"+data.allpers[x]+"</td>";
      a += "<td style='text-align:center'>";
      a += '<input type="checkbox" id="per'+la+'" class="pidselect"/ perid="'+x+'" '+((b==-1)?"":"checked=checked")+'>';
      a += '<label for="per'+la+'"><i class="fa fa-toggle-on fa-lg"></i><i class="fa fa-toggle-off fa-lg"></i></label>';
      a += '</td></tr>';
      la++;
    }
    return a;
  },
  ajaxpermissionget: function(userid){
    popup.openloading();
    $.ajax({
      url: "/user/getpermission",
      type:"post",
      dataType:'json',
      data:{
        "uid": userid,
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          $("#permissiontables tbody").html(preference.buildpertable(data));
          $("#edituserpermission").modal('show');
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  getUserPers: function(obj){
    var self = this;
    self.permiss = [];
    $(obj).each(function(){
      if($(this).prop("checked")){
        self.permiss.push($(this).attr("perid"));
      }
    });
  },
  ajaxsetPermission: function(){
    var self = this;
    popup.openloading();
    self.getUserPers("#permissiontables .pidselect");
    $.ajax({
        url: "/user/permissionset",
        type:"post",
        dataType:'json',
        data:{
          "uid": self.permissid,
          "premission": JSON.stringify(self.permiss)
        },
        success: function(data){
          popup.closeloading();
          if(data.code == "10"){
            $("#edituserpermission").modal('hide');
          }
          popup.openwarning(data.msg);
        },
        error: function(){
          popup.closeloading();
          popup.openwarning("unknow error");
        }
    });
  },
  onload: function(){
    var self = this;
    $("#preferencenav .message").click(function(){
      $("#preferencenav .active").removeClass("active");
      $(this).parent().addClass("active");
      $("#preference .navshow").removeClass("navshow");
      var active = $(this).attr("active");
      $("#"+active).addClass("navshow");
    });
    $("#changepwd .save").click(function(){
      self.ajaxchangpwd();
    });
    $("#menufun .adduser").click(function(){
      self.cleanadduser();
      $("#adduserbox").modal('show');
    });
    $("#adduserbox .addusersubmit").click(function(){
      self.ajaxadduser();
    });
    $("#usermanage").on("click", "tbody .fa-trash-o", function(){ //delete user
      var userid = $(this).parent().parent().attr("userid");
      self.ajaxdeluser(userid);
    });
    $("#usermanage").on("click", "tbody .fa-edit", function(){ //edit user
      var userid = $(this).parent().parent().attr("userid");
      self.cleanadmincpw();
      self.getadmininfo(userid);
    });
    $("#edituserbox .changepwdsubmit").click(function(){
      self.adminchangepwd();
    });
    $("#usertables").on("click", "tbody .fa-exclamation-circle", function(){
      var userid = $(this).parent().parent().attr("userid");
      self.permissid = userid;
      self.ajaxpermissionget(userid);
    });
    $("#edituserpermission").on("click", ".changepresubmit", function(){
      self.ajaxsetPermission();
    });
  }
}

var webpage = {
  editpageid:null,
  cleaninput:function(){
    $("#addpage .pagename").val('');
    $("#addpage .pagetitle").val('');
    CKEDITOR.instances.editor1.setData('');
  },
  ajaxarticleup:function(){
    var test = [
      [$("#addpage .pagename"), "tnonull", "the pagename is empty"],
      [$("#addpage .pagetitle"), "tnonull", "the pagetitle is empty"],
    ];
    if(!formstr.tallobj(test))
      return false;
    popup.openloading();
    $.ajax({
      url: "/article/articleadd",
      type:"post",
      dataType:'json',
      data:{
        "articleadd[pagename]": $("#addpage .pagename").val(),
        "articleadd[pagetitle]": $("#addpage .pagetitle").val(),
        "articleadd[content]": CKEDITOR.instances.editor1.getData(),
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          webpage.cleaninput();
          webpage.ajaxpagelist();
          webpage.gotolist();
          popup.openwarning(data.msg);
          return true;
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxdelarticle: function(pageid){
    popup.openloading();
    $.ajax({
      url: "/article/deletearticle",
      type:"post",
      dataType:'json',
      data:{
        "articledel[pageid]": pageid,
      },
      success: function(data){
        popup.closeloading();
        if(data.code == '10'){
          popup.openwarning(data.msg);
          webpage.ajaxpagelist();
          return true;
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  gotolist:function(){
    $("#pagmanagenav .active").removeClass("active");
    $("#pagmanagenav li").eq(0).addClass("active");
    $("#pagmanage .navshow").removeClass("navshow");
    $("#pagelist").addClass("navshow");
  },
  ajaxpagelist:function(){
    popup.openloading();
    $.ajax({
      url: "/article/articlelist",
      type:"post",
      dataType:'json',
      success:function(data){
        popup.closeloading();
        if(data.code == "10"){
          webpage.buildlist(data['list']);
          return true;
        }
        if(data.code == "9")
          webpage.buildlist({});
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  pagelisttable: null,
  buildlist: function(data){
    if(this.pagelisttable)
     this.pagelisttable.destroy();
    $('#pagelisttable tbody').empty();
    this.pagelisttable = $('#pagelisttable').DataTable( {
      "data": data,
      "columns": [
        { "data": "pagename" },
        { "data": "pagetitle" },
        {
          "data": "pageid",
          "render": function ( data, type, row ) {
            if(!data)
              return "";
            return '<a target="_blank" href="/page/'+data+'">' + pagecode.hosts +'/page/' + data + "</a>";
          },
        },
        { "data": "submiter" },
        { "data": "edittime" },
        {
          "class": "t-center",
          "data": null,
          "defaultContent": '<i class="fa fa-edit fa-lg"></i>'
        },
        {
          "class": "t-center",
          "data": null,
          "defaultContent": '<i class="fa fa-trash-o fa-lg"></i>'
        },
      ],
      "rowCallback": function( row, data ) {
          $(row).attr('pageid',data.pageid);
      },
    } );
  },
  getarticle: function(pageid){
    popup.openloading();
    $.ajax({
      url: "/article/getarticle",
      type:"post",
      dataType:'json',
      data:{
        "articleinfo[pageid]": pageid,
      },
      success:function(data){
        popup.closeloading();
        if(data.code == "10"){
          webpage.editpageid = data.article.pageid;
          $("#editpage .pagename").val(data.article.pagename);
          $("#editpage .pagetitle").val(data.article.pagetitle);
          CKEDITOR.instances.editor2.setData(data.article.content);
          $("#pagmanage .navshow").removeClass("navshow");
          $("#editpage").addClass("navshow");
          return true;
        }
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  updatearticle:function(){
    var test = [
      [$("#editpage .pagename"), "tnonull", "the pagename is empty"],
      [$("#editpage .pagetitle"), "tnonull", "the pagetitle is empty"],
    ];
    if(!formstr.tallobj(test))
      return false;
    popup.openloading();
    $.ajax({
      url: "/article/editarticle",
      type:"post",
      dataType:'json',
      data:{
        "articleedit[pageid]": webpage.editpageid,
        "articleedit[pagename]":$("#editpage .pagename").val(),
        "articleedit[pagetitle]":$("#editpage .pagetitle").val(),
        "articleedit[content]":CKEDITOR.instances.editor2.getData(),
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          webpage.ajaxpagelist();
          webpage.gotolist();
          popup.openwarning(data.msg);
          return true;
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  onload: function(){
    var self = this;
    $("#pagmanagenav .message").click(function(){
      $("#pagmanagenav .active").removeClass("active");
      $(this).parent().addClass("active");
      $("#pagmanage .navshow").removeClass("navshow");
      var active = $(this).attr("active");
      $("#"+active).addClass("navshow");
    });
    $("#articlesubmit").click(function(){
      self.ajaxarticleup();
    });
    $("#pagelist").on("click","tbody .fa-trash-o" ,function(){
      var pageid = $(this).parent().parent().attr("pageid");
      self.ajaxdelarticle(pageid);
    });
    $("#pagelist").on("click","tbody .fa-edit" ,function(){
      var pageid = $(this).parent().parent().attr("pageid");
      self.getarticle(pageid);
    });
    $("#editpagesubmit").click(function(){
      self.updatearticle();
    });
    $("#editpage .fa-mail-reply").click(function(){
      webpage.gotolist();
    });
  }
}

var groupnews = {
  grouptagid:null,
  mediaid:null,
  buildnewssend: function(obj){
    var self = this;
    var popobj = obj.parent().prev();
    $("#newsselect").html(popobj.clone().html());
    self.mediaid = obj.attr("media-id");
    self.ajaxgetgroups();
  },
  buildgroupscontrol: function(data){
    var a = "<option value='none'>Choose Tag</option>";
    var la = data.length;
    for(var i=0; i<la; i++){
      a += "<option value='"+data[i]['id']+"'>"+data[i]['name']+"</option>";
    }
    return a;
  },
  ajaxgetgroups: function(){
    popup.openloading();
    $.ajax({
      url: "/wechat/getgrouptags",
      type:"post",
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          $("#groupscontrol").html(groupnews.buildgroupscontrol(data.tags));
          $("#groupnewspop").modal('show');
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    })
  },
  ajaxsendpre: function(){
    if($("#groupscontrol").val() == "none"){
      popup.openwarning('please choose a group');
      return false;
    }
    popup.openloading();
    $.ajax({
      url: "/wechat/setsendnewsevent",
      type:"post",
      dataType:'json',
      data:{
        grouptagid: $("#groupscontrol").val(),
        mediaid: groupnews.mediaid,
        groupname: $("#groupscontrol").find("option:selected").text(),
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          groupnews.createtemppage(data.tempid);
          $("#groupnewspop").modal('hide');
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  createtemppage: function(code){
    var self = this;
    var temp = $(document.createElement('div'));
    temp.addClass("autoclosediv");
    var html = '<div><div style="font-size:30px;color:red;font-weight:bold;height:30%"></div>';
    html += '<div style="height:30%;text-align:left;font-size:20px;padding:10px">';
    html += 'please within 100s send below code to MaxMara Wechat. so that this News can be send your a news preview';
    html += '</div><div style="height:40%;font-size:20px;color:blue;padding-top:30px">'+code;
    html += '</div></div>';
    temp.html(html);
    temp.appendTo("body");
    self.autotemppage(temp,100);
  },
  autotemppage: function(obj,att){
    if(att<=0){
      obj.fadeOut(function(){
        this.remove();
      });
      return true;
    }
    obj.children().children().eq(0).text(att);
    att--;
    setTimeout(function(){groupnews.autotemppage(obj,att)}, '1000');
  },
  groupnewscontent: function(data){//AAAAAAAAAAA
    var a = "";
    for(var x in data){
      var la = data[x].length;
      var updatetime = 0;
      if(la>0)
        updatetime = data[x]['0']["update_time"];
      a += '<div style="float:left;padding:20px" newstime="'+updatetime+'" class="groupnews-card">';
      a += '<div class="newscard">';
      a += '<div>';
      a += '<div>';
      a += '<ul>';
      if(la>0){
        a += '<li style="background-repeat: no-repeat;background-size:cover;background-image:url('+"'/cimg.php?style=w_400&amp;image="+encodeURIComponent(data[x]['0']['thumb_url'])+"'"+')">';
        a += '<p>'+data[x]['0']['title']+'</p>';
        a += '<a href="'+data[x]['0']['url']+'" target="_blank">点击预览</a>';
        a += '</li>';
      }
      for(var i=1; i<la; i++){
        a += '<li>'
        a += '<p>'+data[x][i]['title']+'</p>';
        a += '<img src="/cimg.php?style=w_400&amp;image='+encodeURIComponent(data[x][i]['thumb_url'])+'">';
        a += '<a href="'+data[x][i]['url']+'" target="_blank">点击预览</a>';
        a += '</li>';
      }
      a += '</ul>';
      a += '</div>';
      a += '</div>';
      a += '<div>';
      a += '<button type="button" class="btn btn-primary groupnewssend" media-id="'+x+'">Group Send</button>';
      a += '</div>';
      a += '</div>';
      a += '</div>';
    }
    return a;
  },
  ajaxloadnewslist: function(itime, order){
    popup.openloading();
    var pdata = {};
    if(itime)
      pdata = {
        timestamp: itime,
        order: order
      };
    $.ajax({
      url:"/wechat/newsget",
      type:"post",
      dataType:'json',
      data: pdata,
      success:function(data){
        popup.closeloading();
        if(data.code == '10'){
          if(this.hasOwnProperty('data') && this.data.hasOwnProperty('order')){
            if(this.data.order == 'bottom')
              $("#groupnewscontent").append(groupnews.groupnewscontent(data.list));
            if(this.data.order == 'top')
              $("#groupnewscontent").prepend(groupnews.groupnewscontent(data.list));
          }else{
            $("#groupnewscontent").append(groupnews.groupnewscontent(data.list));
          }
        }
        popup.openwarning(data.msg);
      },
      error:function(){
        popup.closeloading();
        popup.openwarning('unknow errors');
      }
    });
  },
  onload: function(){
    var self = this;
    $("#groupnewspanel").on("click", ".groupnewssend", function(){
      self.buildnewssend($(this));
    });
    $("#groupnewssubmit").click(function(){
      self.ajaxsendpre();
    });
    $(".groupnews-sync").on("click", "", function(){
      var itime = $("#groupnewscontent").children('.groupnews-card').first().attr("newstime");
      if(!itime)
        itime = 1;
      groupnews.ajaxloadnewslist(itime, 'top');
    });
    $(".groupnews-renew").on("click", "", function(){
      var itime = $("#groupnewscontent").children('.groupnews-card').last().attr("newstime");
      groupnews.ajaxloadnewslist(itime, 'bottom');
    });
  }
}

var jssdk = {
  gotolist: function(){
    $("#jssdkmanagepanel .navshow").each(function(){
      $(this).removeClass("navshow");
    });
    $("#jssdknav .active").each(function(){
      $(this).removeClass("active");
    });
    $("#jssdknav li").eq(0).addClass("active");
    $("#jssdklist").addClass("navshow");
  },
  gotoedit: function(){
    $("#jssdkmanagepanel .navshow").each(function(){
      $(this).removeClass("navshow");
    });
    $("#editjssdk").addClass("navshow");
  },
  ajaxloadlist: function(){
    popup.openloading();
    $.ajax({
      url: "/wechat/jssdk/list",
      type:"post",
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          jssdk.buildjssdklist(data.list);
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  initaddjssdk: function(){
    var jssdconfig = "wx.config({\n debug: true,\n";
    jssdconfig += " appId: wxsdk.appid,  //Not allowed to modify\n";
    jssdconfig += " timestamp: wxsdk.time,  //Not allowed to modify\n";
    jssdconfig += " nonceStr: wxsdk.noncestr,  //Not allowed to modify\n";
    jssdconfig += " signature: wxsdk.sign,  //Not allowed to modify\n";
    jssdconfig += " jsApiList: [\n\n   //allowed wechat API list\n";
    jssdconfig += "  'checkJsApi',\n  'onMenuShareTimeline',\n  'onMenuShareAppMessage',\n  'onMenuShareQQ',\n  'onMenuShareWeibo',\n  'hideMenuItems',\n";
    jssdconfig += "  'showMenuItems',\n  'hideAllNonBaseMenuItem',\n  'showAllNonBaseMenuItem',\n  'getNetworkType',\n  'openLocation',\n  'getLocation',\n  'hideOptionMenu',\n  'showOptionMenu',\n  'closeWindow'\n";
    jssdconfig += "  ]\n});\n"
    // $("#addjssdk .jssdconfig").val(jssdconfig);
    $("#addjssdk .jssdkdomain").val('');
    $("#addjssdk .jssdkname").val('');
  },
  ajaxaddjssdk: function(){
    popup.openloading();
    $.ajax({
      url: "/wechat/jssdk/build",
      type:"post",
      data: {
        // jscontent: $("#addjssdk .jssdconfig").val(),
        domain: $("#addjssdk .jssdkdomain").val(),
        name: $("#addjssdk .jssdkname").val(),
      },
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          jssdk.ajaxloadlist();
          jssdk.gotolist();
          jssdk.initaddjssdk();
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxjssdkinfo: function(id){
    popup.openloading();
    $.ajax({
      url: "/wechat/jssdk/info",
      type:"post",
      data: {
        id: id,
      },
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          // $("#editjssdk .jssdconfig").val(data.info.jscontent);
          $("#editjssdk .jssdkdomain").val(data.info.domain);
          $("#editjssdk .jssdkname").val(data.info.name);
          jssdk.gotoedit();
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  jssdkeditid: null,
  ajaxjssdkupdate: function(){
    var self = this;
    popup.openloading();
    $.ajax({
      url: "/wechat/jssdk/update",
      type:"post",
      data: {
          id: self.jssdkeditid,
          // jscontent: $("#editjssdk .jssdconfig").val(),
          domain: $("#editjssdk .jssdkdomain").val(),
          name: $("#editjssdk .jssdkname").val(),
      },
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          jssdk.ajaxloadlist();
          jssdk.gotolist();
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxjssdkdel: function(){
    var self = this;
    popup.openloading();
    $.ajax({
      url: "/wechat/jssdk/delete",
      type:"post",
      data: {
          id: self.jssdkeditid,
      },
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          jssdk.ajaxloadlist();
          jssdk.gotolist();
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  jssdklisttable: null,
  buildjssdklist: function(data){
    if(this.jssdklisttable)
     this.jssdklisttable.destroy();
    $('#jssdklisttable tbody').empty();
    this.jssdklisttable = $('#jssdklisttable').DataTable( {
      "data": data,
      "columns": [
        { "data": "name" },
        {
          "data": "jsfilename",
          "render": function ( data, type, row ) {
            if(!data)
              return "";
            return '<a target="_blank" href="/api/v1/js/'+data+'/wechat">' + pagecode.hosts +'/api/v1/js/'+data+"/wechat</a>";
          },
        },
        { "data": "username" },
        {
          "class": "t-center",
          "data": null,
          "defaultContent": '<i class="fa fa-edit fa-lg"></i>'
        },
        {
          "class": "t-center",
          "data": null,
          "defaultContent": '<i class="fa fa-trash-o fa-lg"></i>'
        },
      ],
      "rowCallback": function( row, data ) {
          $(row).attr('jsid',data.id);
      },
    } );
  },
  onload: function(){
    var self = this;
    $("#jssdknav .message").click(function(){
      $("#jssdknav .active").each(function(){
        $(this).removeClass("active");
      });
      active = $(this).attr("active");
      $("#jssdkmanagepanel .navshow").each(function(){
        $(this).removeClass("navshow");
      });
      $("#"+active).addClass("navshow");
      if(active == 'addjssdk')
        jssdk.initaddjssdk();
      $(this).parent().addClass("active");
    });
    $("#jssdkmanagepanel .fa-mail-reply").click(function(){
      self.gotolist();
    });
    $("#addjssdksubmit").click(function(){
      self.ajaxaddjssdk();
    });
    $("#jssdklist").on("click", "#jssdklisttable .fa-edit", function(){
      self.jssdkeditid = $(this).parent().parent().attr('jsid');
      self.ajaxjssdkinfo(self.jssdkeditid);
    });
    $("#editjssdksubmit").on("click",function(){
      self.ajaxjssdkupdate();
    });
    $("#jssdklist").on("click", "#jssdklisttable .fa-trash-o", function(){
      self.jssdkeditid = $(this).parent().parent().attr('jsid');
      self.ajaxjssdkdel(self.jssdkeditid);
    });
  },
};

var publicall = {
  gotolist: function(){
    $(".navselect .active").each(function(){
      $(this).removeClass("active");
    });
    $(".mainmanage .navshow").each(function(){
      $(this).removeClass("navshow");
    });
    $(".navselect li").eq(0).addClass("active");
    $(".mainmanage .navhide").eq(0).addClass("navshow");
  },
  gotoedit: function(id){
    $(".mainmanage .navshow").each(function(){
      $(this).removeClass("navshow");
    });
    $("#"+id).addClass("navshow");
  },
  activemenu: function(idname, obj){
    $("#"+idname+" .buttontype>.btn-default").each(function(){
      $(this).removeClass("active");
    });
    $("#"+idname+" .menushow").each(function(){
      $(this).removeClass("menushow");
    });
    obj.addClass("active");
    var action = obj.attr("action");
    $("#"+idname+" ."+action).addClass("menushow");
    if($("#"+idname+" ."+action+" div").length == 0)
      $("#"+idname+" ."+action).html(htmlconetnt[action]());
  },
  emptyactive: function(idname){
    $("#"+idname+" .buttontype>.btn-default").each(function(){
      $(this).removeClass("active");
    });
    $("#"+idname+" .menushow").each(function(){
      $(this).removeClass("menushow");
    });
    $("#"+idname+" .sonshow").each(function(){
      $(this).empty();
    });
  },
  onload: function(){
    $(".navselect").on("click", ".message", function(){
      var active = $(this).attr("active");
      $(".navselect .active").each(function(){
        $(this).removeClass("active");
      });
      $(".mainmanage .navshow").each(function(){
        $(this).removeClass("navshow");
      });
      $(this).parent().addClass("active");
      $("#"+active).addClass("navshow");
    });
  }
}

$(function(){
  menu.onload();
  keyword.onload();
  autoreplay.onload();
  preference.onload();
  webpage.onload();
  groupnews.onload();
  jssdk.onload();
  publicall.onload();
});
