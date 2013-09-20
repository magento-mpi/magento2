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
     * @var Magento_Widget_Model_Widget_ConfigFactory
     */
    protected $_configFactory;

    /**
     * @param Magento_Widget_Model_Widget_ConfigFactory $configFactory
     */
    public function __construct(Magento_Widget_Model_Widget_ConfigFactory $configFactory)
    {
        $this->_configFactory = $configFactory;
    }

    /**
     * Add additional settings to wysiwyg config for Widgets Insertion Plugin
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Widget_Model_Observer
     */
    public function prepareWidgetsPluginConfig(Magento_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();

        if ($config->getData('add_widgets')) {
            $settings = $this->_configFactory->create()->getPluginSettings($config);
            $config->addData($settings);
        }
        return $this;
    }

}
