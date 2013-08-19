<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Enterprise_CustomerSegment_Model_Logging
 *
 * Model for logging event related to Customer Segment, active only if Enterprise_Logging module is enabled
 */
class Enterprise_CustomerSegment_Model_Logging
{
    /**
     * @var Enterprise_CustomerSegment_Model_Resource_Segment|null
     */
    protected $_resourceModel = null;

    /**
     * @var Magento_Core_Controller_Request_Http|null
     */
    protected $_request = null;

    /**
     * @param Enterprise_CustomerSegment_Model_Resource_Segment $resourceModel
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(
        Enterprise_CustomerSegment_Model_Resource_Segment $resourceModel,
        Magento_Core_Controller_Request_Http $request
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_request = $request;
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
            __('Matched %1 Customers of Segment %2', $customersQty, $segmentId)
            : '-'
        );
    }
}
