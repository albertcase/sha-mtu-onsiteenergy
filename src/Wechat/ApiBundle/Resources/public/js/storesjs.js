var stores = {
  ajaxloadlist: function(){
    popup.openloading();
    $.ajax({
      url: "/wechat/stores/list",
      type:"post",
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          stores.buildlist(data.list);
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
    $('#storeslisttable tbody').empty();
    this.buildlisttable = $('#storeslisttable').DataTable( {
      "data": data,
      "columns": [
        {
          "data": "storename",
          "class": "t-center"
        },
        {
          "class": "t-center",
          "data": "phone"
        },
        {
          "data": "brandtype",
          "class": "t-center"
        },
        {
          "data": "storelog",
          "render": function ( data, type, row ) {
            if(!data)
              return "";
            return "<img src='"+data+"'/ style='width:120px'>";
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
          $(row).attr('storeid',data.id);
      },
    } );
  },
  ajaxadd: function(){
    popup.openloading();
    $.ajax({
      url: "/wechat/stores/add",
      type:"post",
      data: {
        'storename': $("#addstores .storename").val(),
        'address': $("#addstores .storeaddress").val(),
        'phone': $("#addstores .storephone").val(),
        'lat': $("#addstores .storelat").val(),
        'lng': $("#addstores .storelng").val(),
        'openhours': $("#addstores .storeopenhours").val(),
        'brandtype': $("#addstores .storebrandtype").val(),
        'storelog': $("#addstores .newspic").attr("src"),
      },
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          stores.ajaxloadlist();
          publicall.gotolist();
          stores.initaddbox();
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  initaddbox: function(){
    $("#addstores .storename").val("");
    $("#addstores .storeaddress").val("");
    $("#addstores .storephone").val("");
    $("#addstores .storelat").val("");
    $("#addstores .storelng").val("");
    $("#addstores .storeopenhours").val("");
    $("#addstores .storebrandtype").val("");
    if($("#addstores .newspic").length)
      fileupload.replaceimage($("#addstores .newspic").prev());
    if($("#addstores .newsfile").length)
      $("#addstores .newsfile").val("");

  },
  initeditbox: function(){
    $("#editstores .storename").val("");
    $("#editstores .storeaddress").val("");
    $("#editstores .storephone").val("");
    $("#editstores .storelat").val("");
    $("#editstores .storelng").val("");
    $("#editstores .storeopenhours").val("");
    $("#editstores .storebrandtype").val("");
    if($("#editstores .newspic").length)
      fileupload.replaceimage($("#editstores .newspic").prev());
    if($("#editstores .newsfile").length)
      $("#editstores .newsfile").val("");
  },
  ajaxeditid: null,
  ajaxinfo: function(){
    var self = this;
    popup.openloading();
    $.ajax({
      url: "/wechat/stores/info",
      type:"post",
      data: {
        "id": self.ajaxeditid,
      },
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          $("#editstores .storename").val(data.info.storename);
          $("#editstores .storeaddress").val(data.info.address);
          $("#editstores .storephone").val(data.info.phone);
          $("#editstores .storelat").val(data.info.lat);
          $("#editstores .storelng").val(data.info.lng);
          $("#editstores .storeopenhours").val(data.info.openhours);
          $("#editstores .storebrandtype").val(data.info.brandtype);
          fileupload.replaceinput(data.info.storelog, $("#editstores .newsfile"));
          publicall.gotoedit("editstores");
        }
        popup.openwarning(data.msg);
      },
      error: function(){
        popup.closeloading();
        popup.openwarning("unknow error");
      }
    });
  },
  ajaxupdate: function(){
    var self = this;
    popup.openloading();
    $.ajax({
      url: "/wechat/stores/update",
      type:"post",
      data: {
        id: self.ajaxeditid,
        'storename': $("#editstores .storename").val(),
        'address': $("#editstores .storeaddress").val(),
        'phone': $("#editstores .storephone").val(),
        'lat': $("#editstores .storelat").val(),
        'lng': $("#editstores .storelng").val(),
        'openhours': $("#editstores .storeopenhours").val(),
        'brandtype': $("#editstores .storebrandtype").val(),
        'storelog': $("#editstores .newspic").attr("src"),
      },
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          stores.ajaxloadlist();
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
  ajaxdelete: function(id){
    popup.openloading();
    $.ajax({
      url: "/wechat/stores/delete",
      type:"post",
      data: {
        "id": id,
      },
      dataType:'json',
      success: function(data){
        popup.closeloading();
        if(data.code == "10"){
          stores.ajaxloadlist();
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
    $("#addstores").on("change", ".newsfile", function(){
      fileupload.sendfiles($(this)[0].files[0], $(this));
    });
    $("#editstores").on("change", ".newsfile", function(){
      fileupload.sendfiles($(this)[0].files[0], $(this));
    });
    $("#editstores").on("click",".fa-times",function(){
      fileupload.replaceimage($(this));
    });
    $("#addstores").on("click",".fa-times",function(){
      fileupload.replaceimage($(this));
    });
    $("#addstoressubmit").on("click", function(){
      self.ajaxadd();
    });
    $("#storeslist").on("click", "#storeslisttable .fa-edit", function(){
      self.ajaxeditid = $(this).parent().parent().attr("storeid");
      self.ajaxinfo();
    });
    $("#editstores").on("click", ".fa-mail-reply", function(){
      self.ajaxloadlist();
    });
    $("#editstoresubmit").on("click", function(){
      self.ajaxupdate();
    });
    $("#storeslist").on("click", "#storeslisttable .fa-trash-o", function(){
      console.log($(this).parent().parent().attr("storeid"));
      self.ajaxdelete($(this).parent().parent().attr("storeid"));
    });
  }
}

$(function(){
  stores.onload();
});
