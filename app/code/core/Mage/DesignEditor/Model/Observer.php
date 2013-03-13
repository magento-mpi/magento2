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
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_DesignEditor_Helper_Data $helper
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_DesignEditor_Helper_Data $helper
    ) {
        $this->_objectManager = $objectManager;
        $this->_helper        = $helper;
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

    /**
     * Remove non-VDE JavaScript assets in design mode
     * Applicable in combination with enabled 'vde_design_mode' flag for 'head' block
     *
     * @param Varien_Event_Observer $event
     */
    public function clearJs(Varien_Event_Observer $event)
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = $event->getEvent()->getLayout();
        $blockHead = $layout->getBlock('head');
        if (!$blockHead || !$blockHead->getData('vde_design_mode')) {
            return;
        }

        $vdeAssets = array();
        /** @var $groups Mage_Page_Model_Asset_PropertyGroup[] */
        $groups = $this->_objectManager->get('Mage_Page_Model_GroupedAssets')->groupByProperties();
        foreach ($groups as $group) {
            if ($group->getProperty('flag_name') == 'vde_design_mode'
                && $group->getProperty(Mage_Page_Model_GroupedAssets::PROPERTY_CONTENT_TYPE)
                    == Mage_Core_Model_Design_Package::CONTENT_TYPE_JS) {
                $vdeAssets = array_merge($vdeAssets, $group->getAll());
            }
        }
        /** @var $assets Mage_Core_Model_Page_Asset_Collection */
        $assets = $this->_objectManager->get('Mage_Core_Model_Page')->getAssets();

        /** @var $nonVdeAssets Mage_Core_Model_Page_Asset_AssetInterface[] */
        $nonVdeAssets = array_diff_key($assets->getAll(), $vdeAssets);

        foreach ($nonVdeAssets as $assetId => $asset) {
            if ($asset->getContentType() == Mage_Core_Model_Design_Package::CONTENT_TYPE_JS) {
                $assets->remove($assetId);
            }
        }
    }
}
