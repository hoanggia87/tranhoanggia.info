// JavaScript Document
function OnSubmitForm(url)
{ 
   document.appForm.action = url;
   document.appForm.submit();
   return true;
}

function checkCheckBox(){
	var theForm = document.appForm;
	if (theForm.elements[i].name=='cid[]')
	{
        theForm.elements[i].checked = checked;
        if(theForm.elements[i].checked = true){
        	window.alert(this.value);
        }
    }
}

var checked=false;
function checkedAll() {
    var theForm = document.appForm;
    if (checked == false)
    {
    	checked = true;
    	//theForm.checkValue.value = theForm.elements.length;
    }
    else
    {
    	checked = false;
    	//theForm.checkValue.value = 0;
    }
    
    var countCheckBox = 0;
    for (i=0; i<theForm.elements.length; i++) {
        if (theForm.elements[i].name=='cid[]'){
            theForm.elements[i].checked = checked;
            countCheckBox++;
        }
    }
    
    if (checked == true)
    {
    	theForm.checkValue.value = countCheckBox;
    }
    else
    {    	
    	theForm.checkValue.value = 0;
    }
}

function urlencode(str)
{
    if (document.all)
    {
        var resultTmp = encodeURI(str);
        // replace $&+,/:;=?@
        resultTmp = resultTmp.replace(/&/gi,'%26').replace(/#/gi,'%23').replace(/=/gi,'%3D').replace(/\?/gi,'%3F').replace(/\+/gi,'%2B');
        return resultTmp;
    }

    var hexStr = function (dec) {
        return '%' + dec.toString(16).toUpperCase();
    };

    var ret = '',
            unreserved = /[\w.-]/, // A-Za-z0-9_.- // Tilde is not here for historical reasons; to preserve it, use rawurlencode instead
            permitList = /[&=%?]/;///[$&+,:;=?@]/;

    str = (str+'').toString();
    for (var i = 0, dl = str.length; i < dl; i++) {
        var ch = str.charAt(i);
        if (!permitList.test(ch)) {
            // Get /n or /r
            var code = str.charCodeAt(i);
            if (code == 13)
            {
                ret += "%0D";//ch;
            }else if(code == 10){
                ret += "%0A";//ch;
            }
            else if(code == 43){
                ret += "%2B";//ch;
            }
            else if(code == 35){
                ret += "%23";//ch;
            }else{
                ret += ch;
            }
        }
        else {
            var code = str.charCodeAt(i);
            // Reserved assumed to be in UTF-8, as in PHP
            if (code === 32) {
                ret += '+'; // %20 in rawurlencode
            }
            else if (code < 128) { // 1 byte
                ret += hexStr(code);
            }
            else if (code >= 128 && code < 2048) { // 2 bytes
                ret += hexStr((code >> 6) | 0xC0);
                ret += hexStr((code & 0x3F) | 0x80);
            }
            else if (code >= 2048 && code < 65536) { // 3 bytes
                ret += hexStr((code >> 12) | 0xE0);
                ret += hexStr(((code >> 6) & 0x3F) | 0x80);
                ret += hexStr((code & 0x3F) | 0x80);
            }
            else if (code >= 65536) { // 4 bytes
                ret += hexStr((code >> 18) | 0xF0);
                ret += hexStr(((code >> 12) & 0x3F) | 0x80);
                ret += hexStr(((code >> 6) & 0x3F) | 0x80);
                ret += hexStr((code & 0x3F) | 0x80);
            }
        }
    }
    return ret;

}
