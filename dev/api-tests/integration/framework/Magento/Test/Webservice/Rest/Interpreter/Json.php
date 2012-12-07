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
 * REST JSON interpreter
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Magento_Test_Webservice_Rest_Interpreter_Json
    implements Magento_Test_Webservice_Rest_Interpreter_Interface
{
    /**
     * Decode Json
     *
     * @param string $input raw json data
     * @return array
     */
    public function decode($input)
    {
        return json_decode($input, true);
    }

    /**
     * Encode Json
     *
     * @param array $input
     * @return string
     */
    public function encode($input)
    {
        return json_encode($input);
    }
}
