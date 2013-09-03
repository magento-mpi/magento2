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
class Magento_Core_Model_Variable_Observer
{
    /**
     * Add variable wysiwyg plugin config
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Core_Model_Variable_Observer
     */
    public function prepareWysiwygPluginConfig(Magento_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();

        if ($config->getData('add_variables')) {
            $settings = Mage::getModel('Magento_Core_Model_Variable_Config')->getWysiwygPluginSettings($config);
            $config->addData($settings);
        }
        return $this;
    }
}
