<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Observer model
 *
 * @category   Magento
 * @package    Magento_Widget
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Widget_Model_Observer
{
    /**
     * Add additional settings to wysiwyg config for Widgets Insertion Plugin
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Widget_Model_Observer
     */
    public function prepareWidgetsPluginConfig(\Magento\Event\Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();

        if ($config->getData('add_widgets')) {
            $settings = Mage::getModel('Magento_Widget_Model_Widget_Config')->getPluginSettings($config);
            $config->addData($settings);
        }
        return $this;
    }

}
