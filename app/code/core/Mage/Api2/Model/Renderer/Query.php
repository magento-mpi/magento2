<?php

class Mage_Api2_Model_Renderer_Query implements Mage_Api2_Model_Renderer_Interface
{
    //const MIME_TYPE = 'text/plain';

    public function render(array $data, $options = null)
    {
        $content = http_build_query($data);

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

        $content = http_build_query(array('messages' => $messages));

        return $content;
    }
}
