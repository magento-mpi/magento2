<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration change observer
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_SystemConfigObserver
{
    /**
     * @var Mage_Launcher_Model_Resource_Page_Collection
     */
    protected $_pageCollection;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_applicationConfig;

    /**
     * Class constructor
     *
     * @param Mage_Core_Model_Config $applicationConfig
     * @param Mage_Launcher_Model_Resource_Page_Collection $pageCollection
     */
    public function __construct(Mage_Core_Model_Config $applicationConfig,
        Mage_Launcher_Model_Resource_Page_Collection $pageCollection
    ) {
        $this->_applicationConfig = $applicationConfig;
        $this->_pageCollection = $pageCollection;
    }

    /**
     * Handle system configuration change (admin_system_config_section_save_after event)
     *
     * @param Varien_Event_Observer $observer
     */
    public function handleSystemConfigChange(Varien_Event_Observer $observer)
    {
        $sectionName = (string)$observer->getEvent()->getSection();
        foreach ($this->_pageCollection as $page) {
            foreach ($page->getTiles() as $tile) {
                $resolvedState = $tile->getStateResolver()
                    ->handleSystemConfigChange($this->_applicationConfig, $sectionName);
                /** @var $tile Mage_Launcher_Model_Tile */
                $tile->setState($resolvedState);
                $tile->save();
            }
        }
    }
}
