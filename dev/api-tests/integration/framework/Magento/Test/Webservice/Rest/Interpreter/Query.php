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
 * REST plain text decoder
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Magento_Test_Webservice_Rest_Interpreter_Query
    implements Magento_Test_Webservice_Rest_Interpreter_Interface
{
    /**
     * Decode text
     *
     * @param string $input raw text data
     * @return string
     */
    public function decode($input)
    {
        return $input;
    }

    /**
     * Encode input array to http query
     *
     * @param $input
     * @return string
     */
    public function encode($input)
    {
        return http_build_query($input);
    }
}
