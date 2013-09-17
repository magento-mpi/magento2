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
 * Model for logging event related to Customer Segment, active only if Magento_Logging module is enabled
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
     * @param Magento_CustomerSegment_Model_Resource_Segment $resourceModel
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(
        Magento_CustomerSegment_Model_Resource_Segment $resourceModel,
        Magento_Core_Controller_Request_Http $request
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_request = $request;
    }

    /**
     * Handler for logging customer segment match
     *
     * @param array $config
     * @param Magento_Logging_Model_Event $eventModel
     * @return Magento_Logging_Model_Event
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function postDispatchCustomerSegmentMatch($config,
        Magento_Logging_Model_Event $eventModel
    ) {
        $segmentId = $this->_request->getParam('id');
        $customersQty = $this->_resourceModel->getSegmentCustomersQty($segmentId);
        return $eventModel->setInfo($segmentId ?
            __('Matched %1 Customers of Segment %2', $customersQty, $segmentId)
            : '-'
        );
    }
}
