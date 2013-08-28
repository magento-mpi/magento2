<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping checkout state
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Multishipping_State extends Magento_Core_Block_Template
{
    public function getSteps()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Type_Multishipping_State')->getSteps();
    }
}
