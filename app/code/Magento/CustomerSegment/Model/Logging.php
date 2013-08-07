<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Magento_CustomerSegment_Model_Logging
 *
 * Model for logging event related to Customer Segment, active only if Enterprise_Logging module is enabled
 */
class Magento_CustomerSegment_Model_Logging
{
    /**
     * @var Magento_CustomerSegment_Model_Resource_Segment|null
     */
    protected $_resourceModel = null;

    /**
     * @var Magento_Core_Controller_Request_Http|null
     */
    protected $_request = null;

    /**
     * @var Magento_CustomerSegment_Helper_Data|null
     */
    protected $_helper = null;

    /**
     * @param Magento_CustomerSegment_Model_Resource_Segment $resourceModel
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_CustomerSegment_Helper_Data $helper
     */
    public function __construct(
        Magento_CustomerSegment_Model_Resource_Segment $resourceModel,
        Magento_Core_Controller_Request_Http $request,
        Magento_CustomerSegment_Helper_Data $helper
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_request = $request;
        $this->_helper = $helper;
    }

    /**
     * Handler for logging customer segment match
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function postDispatchCustomerSegmentMatch(Magento_Simplexml_Element $config,
        Enterprise_Logging_Model_Event $eventModel
    ) {
        $segmentId = $this->_request->getParam('id');
        $customersQty = $this->_resourceModel->getSegmentCustomersQty($segmentId);
        return $eventModel->setInfo($segmentId ?
            $this->_helper->__('Matched %d Customers of Segment %s', $customersQty, $segmentId)
            : '-'
        );
    }
}
