<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sendfriend Observer
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sendfriend_Model_Observer
{
    /**
     * Register Sendfriend Model in global registry
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Sendfriend_Model_Observer
     */
    public function register(\Magento\Event\Observer $observer)
    {
        Mage::getModel('Magento_Sendfriend_Model_Sendfriend')->register();
        return $this;
    }
}
