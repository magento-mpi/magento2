<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Observer model
 *
 * @category   Mage
 * @package    Mage_Widget
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Model_Observer
{
    /**
     * Add additional settings to wysiwyg config for Widgets Insertion Plugin
     *
     * @param Magento_Event_Observer $observer
     * @return Mage_Widget_Model_Observer
     */
    public function prepareWidgetsPluginConfig(Magento_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();

        if ($config->getData('add_widgets')) {
            $settings = Mage::getModel('Mage_Widget_Model_Widget_Config')->getPluginSettings($config);
            $config->addData($settings);
        }
        return $this;
    }

}
