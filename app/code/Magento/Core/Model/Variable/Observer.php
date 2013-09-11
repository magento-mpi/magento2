<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Variable observer
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Variable;

class Observer
{
    /**
     * Add variable wysiwyg plugin config
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Core\Model\Variable\Observer
     */
    public function prepareWysiwygPluginConfig(\Magento\Event\Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();

        if ($config->getData('add_variables')) {
            $settings = \Mage::getModel('Magento\Core\Model\Variable\Config')->getWysiwygPluginSettings($config);
            $config->addData($settings);
        }
        return $this;
    }
}
