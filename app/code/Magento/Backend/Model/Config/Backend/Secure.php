<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Backend_Secure extends Magento_Core_Model_Config_Value
{
    /**
     * @var Magento_Core_Model_Page_Asset_MergeService
     */
    protected $_mergeService;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Page_Asset_MergeService $mergeService
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Page_Asset_MergeService $mergeService,
        Magento_Core_Model_Resource_Abstract $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
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
