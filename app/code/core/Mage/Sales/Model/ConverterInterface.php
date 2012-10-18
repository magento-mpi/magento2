<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Converter interface
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Sales_Model_ConverterInterface
{
    /**
     * Decode data
     *
     * @param Mage_Core_Model_Abstract $object
     * @param $filedName
     * @return mixed
     */
    public function decode(Mage_Core_Model_Abstract $object, $filedName);

    /**
     * Encode data
     *
     * @param Mage_Core_Model_Abstract $object
     * @param $filedName
     * @return mixed
     */
    public function encode(Mage_Core_Model_Abstract $object, $filedName);
}
