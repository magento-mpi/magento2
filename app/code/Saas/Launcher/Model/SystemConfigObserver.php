<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration change observer
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_SystemConfigObserver
{
    /**
     * @var Saas_Launcher_Model_Resource_Page_Collection
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
     * @param Saas_Launcher_Model_Resource_Page_Collection $pageCollection
     */
    public function __construct(Mage_Core_Model_Config $applicationConfig,
        Saas_Launcher_Model_Resource_Page_Collection $pageCollection
    ) {
        $this->_applicationConfig = $applicationConfig;
        $this->_pageCollection = $pageCollection;
    }

    /**
     * Handle system configuration change (admin_system_config_section_save_after event)
     *
     * @param Magento_Event_Observer $observer
     */
    public function handleSystemConfigChange(Magento_Event_Observer $observer)
    {
        $sectionName = (string)$observer->getEvent()->getSection();
        foreach ($this->_pageCollection as $page) {
            foreach ($page->getTiles() as $tile) {
                $resolvedState = $tile->getStateResolver()
                    ->handleSystemConfigChange($sectionName, $tile->getState());
                /** @var $tile Saas_Launcher_Model_Tile */
                $tile->setState($resolvedState);
                $tile->save();
            }
        }
    }
}
