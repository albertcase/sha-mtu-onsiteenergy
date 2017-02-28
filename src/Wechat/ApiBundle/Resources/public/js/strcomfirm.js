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
    var tname = new RegExp("^.{1,300}$");
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
  
}
