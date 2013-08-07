<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Address renderer interface
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Customer_Block_Address_Renderer_Interface
{
    /**
     * Set format type object
     *
     * @param Magento_Object $type
     */
    function setType(Magento_Object $type);

    /**
     * Retrive format type object
     *
     * @return Magento_Object
     */
    function getType();

    /**
     * Render address
     *
     * @param Mage_Customer_Model_Address_Abstract $address
     * @return mixed
     */
    function render(Mage_Customer_Model_Address_Abstract $address);
}
