(function($){
        if(typeof papago_user_class === 'undefined') papago_user_class = '';
        $("#papago_lang_select").insertBefore("#comment");
        $(".fbItem .action").prepend('<a href="#" onclick="translateContext($(this)); return false;" class="' + papago_user_class +' translating-comment"><i class="xi-exchange"></i> 번역하기</a>');

})(jQuery);



function translateContext($translating_btn) {
    var cmt_content = $translating_btn.parents().siblings('.xe_content');
    var cmt_id = $translating_btn.parents('.fbItem').attr('id');
    var cmt_val = cmt_content.text();
    var loading_dom = $('<div id="papago_' + cmt_id + '" class="translated-contents"><i class="xi-spinner-3 xi-spin"></i></div>');
    cmt_content.append(loading_dom);
    var params = new Array();
    params['papago_action'] = 'doTranslate';
    params['papago_value'] = cmt_val;
    params['papago_lang'] = $('#papago_lang').val();
    exec_xml('board', 'dispContent', params, showTranslated, new Array('error', 'message', 'papago_code', 'translated_content'), $translating_btn);
    // exec_json('board.dispContent', params, showTranslated, errorTranslated)

}

function showTranslated(ret_obj, res_tags, translating_btn) {
    var cmt_content = translating_btn.parents().siblings('.xe_content');
    var cmt_id = translating_btn.parents('.fbItem').attr('id');
    var papago_loading_id = '#papago_' + cmt_id;
    $(papago_loading_id).remove();
    if (ret_obj.papago_code) 
    {     
        var content = '<span style="color:red;">' +  ret_obj.message + '</span>';
    } 
    else 
    {
        var content = ret_obj.translated_content;
    }
    
    // translating_btn.addClass('disabled');
    translating_btn.hide();

    var result_content = $('<div class="translated-contents">' + content + '</div>');
    cmt_content.append(result_content);
}

function errorTranslated(ret_obj) {
    console.log(ret_obj);
    alert(ret_obj);
}

function refreshTranslated() {
    $('.translated-contents').remove();
    $('.translating-comment').show();
}

function exec_papago(module, act, params, callback_func, response_tags, callback_func_arg, fo_obj) {
    var xml_path = request_uri+"index.php";
    if(!params) params = {};

    // {{{ set parameters
    if($.isArray(params)) params = arr2obj(params);
    params.module = module;
    params.act    = act;

    if(typeof(xeVid)!='undefined') params.vid = xeVid;
    if(typeof(response_tags) == "undefined" || response_tags.length<1) {
        response_tags = ['error','message'];
    } else {
        response_tags.push('error', 'message');
    }
    // }}} set parameters
    var xml = [];
    var xmlHelper = function(params) {
        var stack = [];

        if ($.isArray(params)) {
            $.each(params, function(key, val) {
                stack.push('<value type="array">' + xmlHelper(val) + '</value>');
            });
        }
        else if ($.isPlainObject(params)) {
            $.each(params, function(key, val) {
                stack.push('<' + key + '>' + xmlHelper(val) + '</' + key + '>');
            });
        }
        else if (!$.isFunction(params)) {
                stack.push(String(params).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;'));
        }

        return stack.join('\n');
    };

    xml.push('<?xml version="1.0" encoding="utf-8" ?>');
    xml.push('<methodCall>');
    xml.push('<params>');
    xml.push(xmlHelper(params));
    xml.push('</params>');
    xml.push('</methodCall>');

    // 전송 성공시
    function onsuccess(data, textStatus, xhr) {
        var resp_xml = $(data).find('response')[0];
        var resp_obj;
        var txt = '';
        var ret = {};
        var tags = {};

        // waiting_obj.css('display', 'none').trigger('cancel_confirm');

        if(!resp_xml) {
            alert(_xhr.responseText);
            return null;
        }

        resp_obj = x2js.xml2json(data).response;

        if (typeof(resp_obj)=='undefined') {
            ret.error = -1;
            ret.message = 'Unexpected error occured.';
            try {
                if(typeof(txt=resp_xml.childNodes[0].firstChild.data)!='undefined') {
                    ret.message += '\r\n'+txt;
                }
            } catch(e){}

            return ret;
        }

        $.each(response_tags, function(key, val){
            tags[val] = true;
        });
        tags.redirect_url = true;
        tags.act = true;
        $.each(resp_obj, function(key, val){ 
            if(tags[key]) ret[key] = val;
        });

        if(ret.error != '0') {
            if ($.isFunction($.exec_xml.onerror)) {
                return $.exec_xml.onerror(module, act, ret, callback_func, response_tags, callback_func_arg, fo_obj);
            }

            alert( (ret.message || 'An unknown error occured while loading ['+module+'.'+act+']').replace(/\\n/g, '\n') );

            return null;
        }

        if(ret.redirect_url) {
            location.href = ret.redirect_url.replace(/&amp;/g, '&');
            return null;
        }

        if($.isFunction(callback_func)) callback_func(ret, response_tags, callback_func_arg, fo_obj);
    }

    try {
        $.ajax({
            url         : xml_path,
            type        : 'POST',
            dataType    : 'xml',
            data        : xml.join('\n'),
            contentType : 'text/plain',
            beforeSend  : function(xhr){ _xhr = xhr; },
            success     : onsuccess,
            error       : function(xhr, textStatus) {
                waiting_obj.css('display', 'none');

                var msg = '';

                if (textStatus == 'parsererror') {
                    msg  = 'The result is not valid XML :\n-------------------------------------\n';

                    if(xhr.responseText === "") return;

                    msg += xhr.responseText.replace(/<[^>]+>/g, '');
                } else {
                    msg = textStatus;
                }

                try{
                    console.log(msg);
                } catch(ee){}
            }
        });
    } catch(e) {
        alert(e);
        return;
    }

}

function arr2obj(arr) {
    var ret = {};
    for(var key in arr) {
        if(arr.hasOwnProperty(key)) ret[key] = arr[key];
    }

    return ret;
}