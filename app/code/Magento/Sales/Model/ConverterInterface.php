<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Converter interface
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Sales_Model_ConverterInterface
{
    /**
     * Decode data
     *
     * @param Magento_Core_Model_Abstract $object
     * @param $filedName
     * @return mixed
     */
    public function decode(Magento_Core_Model_Abstract $object, $filedName);

    /**
     * Encode data
     *
     * @param Magento_Core_Model_Abstract $object
     * @param $filedName
     * @return mixed
     */
    public function encode(Magento_Core_Model_Abstract $object, $filedName);
}
