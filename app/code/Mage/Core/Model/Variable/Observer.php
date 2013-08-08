<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Variable observer
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Variable_Observer
{
    /**
     * Add variable wysiwyg plugin config
     *
     * @param Magento_Event_Observer $observer
     * @return Mage_Core_Model_Variable_Observer
     */
    public function prepareWysiwygPluginConfig(Magento_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();

        if ($config->getData('add_variables')) {
            $settings = Mage::getModel('Mage_Core_Model_Variable_Config')->getWysiwygPluginSettings($config);
            $config->addData($settings);
        }
        return $this;
    }
}
