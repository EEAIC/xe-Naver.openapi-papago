(function($){
	$(function() {

        $("#papago_lang_select").insertBefore("#comment");
        $(".fbItem .action").prepend('<a href="#" onclick="translateContext($(this)); return false;" id=' + '"' + "translatingComment"  + '"' + '><i class="xi-exchange"></i>번역하기</a>');
    


    });

})(jQuery);



function translateContext($link) {
    var value = $link.parents().siblings('.xe_content').text();
    var params = new Array();
    params['papago_action'] = 'doTranslate';
    params['papago_value'] = value;
    params['papago_lang'] = $('#papago_lang').val();
    exec_xml('board', 'dispContent', params, showTranslated, new Array('view'));

}

function showTranslated(ret_obj) {

    console.log(ret_obj.view);
    alert(ret_obj.view);
}