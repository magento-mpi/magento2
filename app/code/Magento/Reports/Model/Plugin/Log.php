<?php
/**
 * Plugin for Magento_Log_Model_Resource_Log model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Reports_Model_Plugin_Log
{
    /**
     * @var Magento_Reports_Model_Event
     */
    protected $_reportEvent;

    /**
     * @var Magento_Reports_Model_Product_Index_Compared
     */
    protected $_comparedProductIdx;

    /**
     * @var Magento_Reports_Model_Product_Index_Viewed
     */
    protected $_viewedProductIdx;

    /**
     * @param Magento_Reports_Model_Event $reportEvent
     * @param Magento_Reports_Model_Product_Index_Compared $comparedProductIdx
     * @param Magento_Reports_Model_Product_Index_Viewed $viewedProductIdx
     */
    public function __construct(
        Magento_Reports_Model_Event $reportEvent,
        Magento_Reports_Model_Product_Index_Compared $comparedProductIdx,
        Magento_Reports_Model_Product_Index_Viewed $viewedProductIdx
    ) {
        $this->_reportEvent = $reportEvent;
        $this->_comparedProductIdx = $comparedProductIdx;
        $this->_viewedProductIdx = $viewedProductIdx;
    }

    /**
     * Clean events by old visitors
     * after plugin for clean method
     *
     * @see Global Log Clean Settings
     *
     * @param Magento_Log_Model_Resource_Log $logResourceModel
     * @return Magento_Log_Model_Resource_Log
     */
    public function afterClean($logResourceModel)
    {
        $this->_reportEvent->clean();
        $this->_comparedProductIdx->clean();
        $this->_viewedProductIdx->clean();
        return $logResourceModel;
    }
}