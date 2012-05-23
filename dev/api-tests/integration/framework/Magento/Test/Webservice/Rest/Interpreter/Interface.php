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
 * REST content type interpreter interface
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
interface Magento_Test_Webservice_Rest_Interpreter_Interface
{
    /**
     * Decode input text to array
     *
     * @param string $input
     * @return array
     */
    public function decode($input);

    /**
     * Encode input array into required format
     * @param array $input
     * @param string
     */
    public function encode($input);
}
