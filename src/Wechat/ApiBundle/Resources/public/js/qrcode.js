var qrcode = {
  ajaxloadlist: function(){
    popup.openloading();
    $.ajax({
      url: "/wechat/qrcode/list",
      type:"post",
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          qrcode.buildlist(data.list);
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxaddqrcode: function(){
    var self = this;
    var up = self[self.aMessageFun]();
    popup.openloading();
    $.ajax({
      url: "/wechat/qrcode/add",
      type:"post",
      dataType:'json',
      data: up,
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          qrcode.ajaxloadlist();
          publicall.gotolist();
          publicall.emptyactive("addqrcode");
          qrcode.aMessageFun = "anone";
          $("#addqrcode .qrcodename").val("");
          $("#addqrcode .qrcodeid").val("");
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  buildlisttable: null,
  buildlist: function(data){
    if(this.buildlisttable)
     this.buildlisttable.destroy();
    $('#qrcodelisttable tbody').empty();
    this.buildlisttable = $('#qrcodelisttable').DataTable( {
      "data": data,
      "columns": [
        {
          "data": "qrName",
          "class": "t-center"
        },
        {
          "class": "t-center",
          "data": "qrSceneid"
        },
        {
          "data": "qrScan",
          "class": "t-center"
        },
        {
          "data": "qrSubscribe",
          "class": "t-center"
        },
        {
          "data": "qrTicket",
          "render": function ( data, type, row ) {
            if(!data)
              return "";
            return "<img src='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket="+data+"'/ style='width:110px'>";
          },
        },
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
          $(row).attr('qrid',data.id);
      },
    } );
  },
  aMessageFun: "anone",
  anone: function(){
    var self = this;
    var a = {
      qrName: $("#addqrcode .qrcodename").val(),
      qrSceneid: $("#addqrcode .qrcodeid").val(),
    };
    return a;
  },
  apushmessage:function(){
    var self = this;
    var a = {
      qrName: $("#addqrcode .qrcodename").val(),
      qrSceneid: $("#addqrcode .qrcodeid").val(),
      MsgType: 'news',
      newslist: menu.getnewslist($("#addqrcode .pushmessage .newslist")),
    };
    return a;
  },
  atextmessage:function(){
    var self = this;
    var a={
      qrName: $("#addqrcode .qrcodename").val(),
      qrSceneid: $("#addqrcode .qrcodeid").val(),
      MsgType: 'text',
      Content: $("#addqrcode .textcontent").val(),
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
  ajaxeditid: null,
  ajaxinfo: function(){
    var self = this;
    popup.openloading();
    $.ajax({
      url: "/wechat/qrcode/info",
      type:"post",
      data: {
        "id": self.ajaxeditid,
      },
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          qrcode.buildinfo(data.info);
          publicall.gotoedit("editqrcode");
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  editid: null,
  buildinfo: function(data){
    var self = this;
    publicall.emptyactive("editqrcode");
    $("#editqrcode .qrcodename").val(data.qrName);
    if(data.MsgType == "text"){
      $("#editqrcode .buttontype .btn").eq(0).addClass("active");
      $("#editqrcode .textmessage").addClass("menushow");
      self.updateFun = "etextmessage";
      $("#editqrcode .textmessage").html(htmlconetnt.atextmessage(data.Content));
      return true;
    }
    if(data.MsgType == "news"){
      $("#editqrcode .buttontype .btn").eq(1).addClass("active");
      $("#editqrcode .pushmessage").addClass("menushow");
      self.updateFun = "epushmessage";
      $("#editqrcode .pushmessage").html(htmlconetnt.apushmessage(data.newslist));
      return true;
    }
    $("#editqrcode .buttontype .btn").eq(2).addClass("active");
    self.updateFun = "enone";
  },
  updateFun: 'none',
  ajaxupdateqrcode: function(){
    var self = this;
    var up = self[self.updateFun]();
    popup.openloading();
    $.ajax({
      url: "/wechat/qrcode/update",
      type:"post",
      dataType:'json',
      data: up,
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          qrcode.ajaxloadlist();
          publicall.gotolist();
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  enone: function(){
    var self = this;
    var a = {
      id: self.ajaxeditid,
      qrName: $("#editqrcode .qrcodename").val()
    };
    return a;
  },
  epushmessage:function(){
    var self = this;
    var a = {
      id: self.ajaxeditid,
      qrName: $("#editqrcode .qrcodename").val(),
      MsgType: 'news',
      newslist: menu.getnewslist($("#editqrcode .pushmessage .newslist")),
    };
    return a;
  },
  etextmessage:function(){
    var self = this;
    var a={
      id: self.ajaxeditid,
      qrName: $("#editqrcode .qrcodename").val(),
      MsgType: 'text',
      Content: $("#editqrcode .textcontent").val(),
    };
    return a;
  },
  ajaxdelete: function(id){
    var self = this;
    popup.openloading();
    $.ajax({
      url: "/wechat/qrcode/delete",
      type:"post",
      dataType:'json',
      data: {
        id: id,
      },
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          qrcode.ajaxloadlist();
          publicall.gotolist();
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
    $("#addqrcodesubmit").click(function(){
      self.ajaxaddqrcode();
    });
    $("#addqrcode").on("click", '.buttontype>.btn-default', function(){
      self.aMessageFun = "a"+($(this).attr("action"));
      publicall.activemenu("addqrcode", $(this));
    });
    // add
    $("#addqrcode").on("change", ".newsfile", function(){
      fileupload.sendfiles($(this)[0].files[0], $(this));
    });
    $("#addqrcode").on("click",".fa-times",function(){
      fileupload.replaceimage($(this));
    });
    $("#addqrcode").on("click",".fa-plus-square", function(){
      var a = htmlconetnt.addnewshtml();
      $(this).after(a);
      $(this).remove();
      if($("#addqrcode .pushmessage .fa-minus-square").length >= 10)
        $("#addqrcode .pushmessage .fa-plus-square").remove();
    });
    $("#addqrcode").on("click",".fa-minus-square", function(){
      $(this).parent().remove();
    });
    // edit
    $("#editqrcode").on("change", ".newsfile", function(){
      fileupload.sendfiles($(this)[0].files[0], $(this));
    });
    $("#editqrcode").on("click",".fa-times",function(){
      fileupload.replaceimage($(this));
    });
    $("#editqrcode").on("click",".fa-plus-square", function(){
      var a = htmlconetnt.addnewshtml();
      $(this).after(a);
      $(this).remove();
      if($("#editqrcode .pushmessage .fa-minus-square").length >= 10)
        $("#editqrcode .pushmessage .fa-plus-square").remove();
    });
    $("#editqrcode").on("click",".fa-minus-square", function(){
      $(this).parent().remove();
    });
    $("#editqrcode").on("click", '.buttontype>.btn-default', function(){
      self.updateFun = "e"+($(this).attr("action"));
      publicall.activemenu("editqrcode", $(this));
    });
    $("#qrcodelist").on("click", "#qrcodelisttable .fa-edit", function(){
      self.ajaxeditid = $(this).parent().parent().attr("qrid");
      self.ajaxinfo();
    });
    $("#qrcodelist").on("click", "#qrcodelisttable .fa-trash-o", function(){
      self.ajaxdelete($(this).parent().parent().attr("qrid"));
    });
    $("#editqrcode").on("click", "#editqrcodesubmit", function(){
      self.ajaxupdateqrcode();
    });
    $("#editqrcode").on("click", ".fa-mail-reply", function(){
      publicall.gotolist();
    });
  }
}

$(function(){
  qrcode.onload();
});
