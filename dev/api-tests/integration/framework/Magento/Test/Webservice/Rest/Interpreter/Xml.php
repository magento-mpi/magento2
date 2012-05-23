<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * REST XML decoder
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Magento_Test_Webservice_Rest_Interpreter_Xml
    implements Magento_Test_Webservice_Rest_Interpreter_Interface
{
    /**
     * Decode XML
     *
     * @param string $input raw xml data
     * @return array
     */
    public function decode($input)
    {
        $xml = new Varien_Simplexml_Element($input);

        return $xml->asCanonicalArray();
    }

    /**
     * Encode input array to XML
     *
     * @param array $input
     * @return string
     */
    public function encode($input)
    {
        $writer = new Zend_Config_Writer_Xml();
        $config = new Zend_Config($input);
        $writer->setConfig($config);
        $xml = $writer->render();
        return $xml;
    }
}
