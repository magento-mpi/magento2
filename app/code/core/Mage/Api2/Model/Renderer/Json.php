<?php

class Mage_Api2_Model_Renderer_Json implements Mage_Api2_Model_Renderer_Interface
{
    //const MIME_TYPE = 'application/json';

    public function render(array $data, $options = null)
    {
        $content = Zend_Json::encode($data);

        return $content;
    }

    public function renderErrors($code, $exceptions)
    {
        $domain = 'core';

        $messages = array();
        /** @var Exception $exception */
        foreach ($exceptions as $exception) {
            $message = array(
                'domain'   => $domain,
                'code'     => $exception->getCode(),
                'message'  => $exception->getMessage(),
            );
            $messages[] = $message;
        }

        $content = Zend_Json::encode(array('messages'=>$messages));

        /*$content = preg_replace('/(:{\")/i', ":{\n\"", $content);
        //$content = preg_replace('/(:{)/i', ":\n{", $content);
        $content = preg_replace('/(,")/i', ",\n\"", $content);
        $content = preg_replace('/("})/i', "\"\n}", $content);
        //$content = preg_replace('/(}})/i', "}\n}", $content);*/

        return $content;
    }
}
