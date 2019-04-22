<?php
/* Copyright (C) LIFOsitory */

if(!defined("__XE__")) exit();

/**
 * @file naver_openapi_papago.addon.php
 * @author LIFOsitory (artherlim@gmail.com)
 * @brief Translator for a comment
 * */
if(!class_exists('NaverPapago', false))
{
    class NaverPapago
    {   
        var $target_acts = NULL;
        var $is_post = true;
        var $headers = array();

        private $addon_info;

        function setInfo(&$addon_info)
        {
            $this->addon_info = $addon_info;
            define('CLIENT_ID', $this->addon_info->client_id);
            define('CLIENT_SECRET', $this->addon_info->client_secret);
        }

        function setHeaders()
        {
            $this->headers[] = "X-Naver-Client-Id: ". CLIENT_ID;
            $this->headers[] = "X-Naver-Client-Secret: ". CLIENT_SECRET;
        }

        function setPath($addon_path)
        {
            $this->addon_path = $addon_path; 
        }

        function loadHtml()
        {   
            Context::loadLang(_XE_PATH_ . 'addons/naver_openapi_papago/lang');
            if (!$this->html) 
                $this->html = TemplateHandler::getInstance()->compile($this->addon_path . '/skin//'. $this->skin, 'view');

            return $this->html;
        }

        function before_module_init(&$ModuleHandler)
        {
           
            $document_srl = Context::get('document_srl');
            if ($document_srl){
                $oDocumentModel = getModel('document');
                $columnList = array('document_srl', 'module_srl', 'comment_count');
                $oDocument = $oDocumentModel->getDocument($document_srl, FALSE, TRUE, $columnList);
                if ($oDocument->getCommentCount() < 1) {
                    echo "no papago";
                } else {
                    $html = $this->loadHtml();
                    Context::addHtmlFooter($html);
                    Context::loadFile(array('./addons/naver_openapi_papago/skin/view.css', '', '', null), true);
                    Context::loadFile(array('./addons/naver_openapi_papago/naver_openapi_papago.js', 'body', '', null), true);
                }
            } 
            return true;
        }

        function before_module_init_doTranslate() {
            $value = Context::get("papago_value");
            $destLang = Context::Get("papago_lang");
            $langCode = $this->detectLangs($value);
           
            $rst = $this->getTranslatedText($langCode['langCode'], $destLang, $value);
            $result = $rst["message"];
            $r = $result["result"];
            printf("<response>\r\n <error>0</error>\r\n <view><![CDATA[%s]]></view>\r\n</response>", $r["translatedText"]);
            Context::close();
            exit();
            
            
        }

   

        function curlInit($url) 
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, $this->is_post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            
            return $ch;
        }

        function detectLangs($input_text)
        {
            $url = "https://openapi.naver.com/v1/papago/detectLangs";
            $encQuery = urlencode($input_text);
            $postvars = "query=".$encQuery;
            $ch = $this->curlInit($url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
            $response = curl_exec ($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close ($ch);

            return json_decode($response, true);

        }

        function getTranslatedText($source, $target, $input_text)
        {
            $url = "https://openapi.naver.com/v1/papago/n2mt";
            $encText = urlencode($input_text);
            $postvars = "source=".$source."&target=".$target."&text=".$encText;
            $ch = $this->curlInit($url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
            $response = curl_exec ($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close ($ch);


            return json_decode($response, true);

        }



    }

    $GLOBALS['__NaverPapago__'] = new NaverPapago;
    $GLOBALS['__NaverPapago__']->setInfo($addon_info);
    $GLOBALS['__NaverPapago__']->setHeaders();
    $GLOBALS['__NaverPapago__']->setPath(_XE_PATH_.'addons/naver_openapi_papago');
    Context::set('oNaverPapago', $GLOBALS['__NaverPapago__']);
}

$oAddonNaverPapago = &$GLOBALS['__NaverPapago__'];

if (method_exists($oAddonNaverPapago, $called_position))
{
    if(!call_user_func_array(array(&$oAddonNaverPapago, $called_position), array(&$this)))
    {
        return false;
    }    
}

$addon_act = Context::get('papago_action');
if($addon_act && method_exists($oAddonNaverPapago, $called_position . '_' . $addon_act))
{
    if(!call_user_func_array(array(&$oAddonNaverPapago, $called_position . '_' . $addon_act), array(&$this)))
    {
        return false;
    }
}