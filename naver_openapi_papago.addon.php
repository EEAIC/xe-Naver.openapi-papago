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
        var $operating_method = NULL;
        var $user_classes;
        private $client_id;
        private $client_secret;
        private $addon_info;

        function setInfo(&$addon_info)
        {
            $this->addon_info = $addon_info;
            $this->client_id = $this->addon_info->client_id;
            $this->client_secret = $this->addon_info->client_secret;
            $this->operating_method = $this->addon_info->operating_method;
            Context::set('papago_operating_method', $this->operating_method);
            $this->user_classes = $this->addon_info->translate_btn_class;
        }

        function setHeaders()
        {
            $this->headers[] = "X-Naver-Client-Id: ". $this->client_id;
            $this->headers[] = "X-Naver-Client-Secret: ". $this->client_secret;
        }

        function setPath($addon_path)
        {
            $this->addon_path = $addon_path; 
        }

        function loadHtml($fileName)
        {   
            Context::loadLang(_XE_PATH_ . 'addons/naver_openapi_papago/lang');
            $html = TemplateHandler::getInstance()->compile($this->addon_path . '/skin//'. $this->skin, $fileName);

            return $html;
        }

        function before_module_init(&$ModuleHandler)
        {
            $document_srl = Context::get('document_srl');
            if ($document_srl){
                $oDocumentModel = getModel('document');
                $columnList = array('document_srl', 'module_srl', 'comment_count');
                $oDocument = $oDocumentModel->getDocument($document_srl, FALSE, TRUE, $columnList);
                if ($oDocument->getCommentCount() < 1) {

                } else {
                    $html = $this->loadHtml('head');
                    if($this->user_classes) 
                    {
                        Context::addHtmlHeader('<script>papago_user_class ="'. $this->user_classes .'";</script>');
                        Context::addHtmlHeader('<script>document_srl ="'. $document_srl .'";</script>');
                    }
                    Context::addHtmlFooter($html);
                    Context::loadFile(array('./addons/naver_openapi_papago/skin/view.css', '', '', null), true);
                    Context::loadFile(array('./addons/naver_openapi_papago/naver_openapi_papago.js', 'body', '', null), true);
                }
            } 
            return true;
        }

        function before_module_init_doTranslate() 
        {
            $value = Context::get("papago_value");
            $destLang = Context::get("papago_lang");
            $langCode = $this->detectLangs($value);
   
            $responseResult = $this->getTranslatedText($langCode['langCode'], $destLang, $value);
            $errorCode = $responseResult["errorCode"];
            $error = 0;
         
            if ($errorCode) 
            {
                $errMessage = $responseResult["errorMessage"];
            }
            else 
            {
                $errorCode = false;
                $resultMessage = $responseResult["message"];
                $translatedContext = $resultMessage["result"]["translatedText"];
                $srcLangType = $resultMessage["result"]["srcLangType"];
                if ($srcLangType == 'zh-CN' || $srcLangType == 'zh-TW') 
                {
                   $srcLangType = str_replace('-', '_', $srcLangType);
                } 
                if ($this->operating_method == 'smt') {
                    $tarLangType = $destLang;
                } 
                else 
                {
                    $tarLangType = $resultMessage["result"]["tarLangType"];
                }
                if ($tarLangType == 'zh-CN' || $tarLangType == 'zh-TW')
                {
                    $tarLangType = str_replace('-', '_', $tarLangType);
                } 
                Context::set('papago_srcLangType', $srcLangType);
                Context::set('papago_tarLangType', $tarLangType);
                $translated_direction = $this->loadHtml('view.direction');
                Context::set('papago_content', $translatedContext);
                $translatedHtml = $this->loadHtml('view');
            }
            
            printf(file_get_contents($this->addon_path . '/tpl/response.result.xml'), $error, $errMessage, $errorCode, $translated_direction, $translatedHtml, $errMessage);
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
            if ($this->operating_method == 'smt') 
            {
                $url = "https://openapi.naver.com/v1/language/translate";
            } 
            else 
            {
                $url = "https://openapi.naver.com/v1/papago/n2mt";
            }
            
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