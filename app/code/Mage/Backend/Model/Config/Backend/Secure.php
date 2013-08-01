<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Backend_Secure extends Mage_Core_Model_Config_Data
{
    /**
     * @var Mage_Core_Model_Page_Asset_MergeService
     */
    protected $_mergeService;

    /**
     * @param Mage_Core_Model_Context $context
     * @param Mage_Core_Model_Page_Asset_MergeService $mergeService
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Mage_Core_Model_Page_Asset_MergeService $mergeService,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_mergeService = $mergeService;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Clean compiled JS/CSS when updating configuration settings
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_mergeService->cleanMergedJsCss();
        }
    }
}
