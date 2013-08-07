<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sendfriend Observer
 *
 * @category    Mage
 * @package     Mage_Sendfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sendfriend_Model_Observer
{
    /**
     * Register Sendfriend Model in global registry
     *
     * @param Magento_Event_Observer $observer
     * @return Mage_Sendfriend_Model_Observer
     */
    public function register(Magento_Event_Observer $observer)
    {
        Mage::getModel('Mage_Sendfriend_Model_Sendfriend')->register();
        return $this;
    }
}
