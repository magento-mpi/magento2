<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Saas search model observer
 *
 * @category   Saas
 * @package    Saas_Search
 */
class Saas_Search_Model_Observer
{
    /**
     * Registry model
     *
     * @var Mage_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * @var Enterprise_Search_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Core_Model_Registry $registry
     * @param Enterprise_Search_Helper_Data $helper
     */
    public function __construct(
        Mage_Core_Model_Registry $registry,
        Enterprise_Search_Helper_Data $helper
    ) {
        $this->_helper = $helper;
        $this->_registryManager = $registry;
    }

    /**
     * Add index version to processor metadata for subsequent saving in cache
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function processorAddMetadataBeforeSave(Varien_Event_Observer $observer)
    {
        if ($this->_helper->isThirdPartSearchEngine()) {
            $indexVersion = $this->_registryManager->registry('search_engine_index_version');
            if ($indexVersion) {
                $processor = $observer->getEvent()->getProcessor();
                $processor->setMetadata('search_engine_index_version', $indexVersion);
            }
        }
    }
}
