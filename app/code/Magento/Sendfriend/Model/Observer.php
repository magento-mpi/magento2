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
namespace Magento\Sendfriend\Model;

class Observer
{
    /**
     * Register Sendfriend Model in global registry
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Sendfriend\Model\Observer
     */
    public function register(\Magento\Event\Observer $observer)
    {
        \Mage::getModel('\Magento\Sendfriend\Model\Sendfriend')->register();
        return $this;
    }
}
