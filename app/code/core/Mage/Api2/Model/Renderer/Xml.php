<?php

class Mage_Api2_Model_Renderer_Xml implements Mage_Api2_Model_Renderer_Interface
{
    //const MIME_TYPE = 'application/xml';

    public function render(array $data, $options = null)
    {
        $value = Zend_XmlRpc_Value::getXmlRpcValue($data);

        $generator = Zend_XmlRpc_Value::getGenerator();
        $generator->openElement('methodResponse')
                  ->openElement('params')
                  ->openElement('param');
        $value->generateXml();
        $generator->closeElement('param')
                  ->closeElement('params')
                  ->closeElement('methodResponse');

        $content = $generator->flush();

        if (isset($options['encoding'])) {
            $content = preg_replace(
                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                '<?xml version="$1" encoding="'.$options['encoding'].'"?>',
                $content
            );
        }

        return $content;
    }

    public function renderErrors($code, $exceptions)
    {
        $content = '<messages>
    <error>
        <domain>:domain</domain>
        <code>:code</code>
        <message>:message</message>
        <extended>:extended</extended>
    </error>
</messages>';

        $domain = 'core';
        $code = 123;
        $message = 'random_string';
        $extended = 'Resource just randomly throw test errors.';
        $replace = array(
            ':domain'   => $domain,
            ':code'     => $code,
            ':message'  => $message,
            ':extended' => $extended,
        );
        $content = strtr($content, $replace);

        $content = preg_replace('/(\>\<)/i', ">\n<", $content);

        /*$content = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
            <SOAP-ENV:Body>
            <SOAP-ENV:Fault>
            <faultcode>' . $code . '</faultcode>
            <faultstring>' . $message . '</faultstring>
            </SOAP-ENV:Fault>
            </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';*/

        //throw new SoapFault($code, $message);

        return $content;
    }
}
