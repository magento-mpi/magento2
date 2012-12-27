<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Observer for design editor module
 */
class Mage_DesignEditor_Model_Observer
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Backend_Model_Session $backendSession
     * @param Mage_Core_Model_Design_Package $design
     * @param Mage_DesignEditor_Helper_Data $helper
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Backend_Model_Session $backendSession,
        Mage_Core_Model_Design_Package $design,
        Mage_DesignEditor_Helper_Data $helper
    ) {
        $this->_objectManager  = $objectManager;
        $this->_backendSession = $backendSession;
        $this->_design         = $design;
        $this->_helper         = $helper;
    }

    /**
     * Set specified design theme
     */
    public function setTheme()
    {
        $themeId = $this->_backendSession->getData('theme_id');
        if ($themeId !== null) {
            $this->_design->setDesignTheme($themeId);
        }
    }

    /**
     * Clear temporary layout updates and layout links
     */
    public function clearLayoutUpdates()
    {
        $daysToExpire = $this->_helper->getDaysToExpire();

        // remove expired links
        /** @var $linkCollection Mage_Core_Model_Resource_Layout_Link_Collection */
        $linkCollection = $this->_objectManager->create('Mage_Core_Model_Resource_Layout_Link_Collection');
        $linkCollection->addTemporaryFilter(true)
            ->addUpdatedDaysBeforeFilter($daysToExpire);

        /** @var $layoutLink Mage_Core_Model_Layout_Link */
        foreach ($linkCollection as $layoutLink) {
            $layoutLink->delete();
        }

        // remove expired updates without links
        /** @var $layoutCollection Mage_Core_Model_Resource_Layout_Update_Collection */
        $layoutCollection = $this->_objectManager->create('Mage_Core_Model_Resource_Layout_Update_Collection');
        $layoutCollection->addNoLinksFilter()
            ->addUpdatedDaysBeforeFilter($daysToExpire);

        /** @var $layoutUpdate Mage_Core_Model_Layout_Update */
        foreach ($layoutCollection as $layoutUpdate) {
            $layoutUpdate->delete();
        }
    }
}
