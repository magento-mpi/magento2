<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping checkout state
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Multishipping_State extends Magento_Core_Block_Template
{
    public function getSteps()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Type_Multishipping_State')->getSteps();
    }
}
