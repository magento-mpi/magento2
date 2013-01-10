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
     * @var Mage_Core_Model_Cache
     */
    protected $_cacheManager;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_DesignEditor_Helper_Data $helper
     * @param Mage_Core_Model_Cache $cacheManager
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_DesignEditor_Helper_Data $helper,
        Mage_Core_Model_Cache $cacheManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_helper        = $helper;
        $this->_cacheManager  = $cacheManager;
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
     * Clear all VDE dependent cache
     */
    public function clearCache()
    {
        // TODO: disable VDE cache here or in Mage_DesignEditor_Model_State to avoid impact on frontend cache
        // TODO: it should be done in scope of MAGETWO-6709
        $this->_cacheManager->flush();
    }
}
