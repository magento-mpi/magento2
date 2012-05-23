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
 * REST content type interpreter factory
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Magento_Test_Webservice_Rest_Interpreter_Factory
{
    /**
     * Default interpreter content type
     */
    const DEFAULT_CONTENT_TYPE = 'text/plain';

    /**
     * Get interpreter object
     *
     * @param string $contentType
     * @return Magento_Test_Webservice_Rest_Interpreter_Interface
     */
    public static function getInterpreter($contentType)
    {
        $interpreters = array(
            'application/xml'  => 'Magento_Test_Webservice_Rest_Interpreter_Xml',
            'application/json' => 'Magento_Test_Webservice_Rest_Interpreter_Json',
            'text/json'        => 'Magento_Test_Webservice_Rest_Interpreter_Json',
            'text/plain'       => 'Magento_Test_Webservice_Rest_Interpreter_Query',
        );

        if (!array_key_exists($contentType, $interpreters)) {
            $contentType = self::DEFAULT_CONTENT_TYPE;
        }

        $interpreterClass = $interpreters[$contentType];
        return new $interpreterClass();
    }
}
