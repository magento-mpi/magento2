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
     * @var Mage_Core_Controller_Request_Http|null
     */
    protected $_request = null;

    /**
     * @var Enterprise_CustomerSegment_Helper_Data|null
     */
    protected $_helper = null;

    /**
     * @param Enterprise_CustomerSegment_Model_Resource_Segment $resourceModel
     * @param Mage_Core_Controller_Request_Http $request
     * @param Enterprise_CustomerSegment_Helper_Data $helper
     */
    public function __construct(Enterprise_CustomerSegment_Model_Resource_Segment $resourceModel,
        Mage_Core_Controller_Request_Http $request, Enterprise_CustomerSegment_Helper_Data $helper
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_request = $request;
        $this->_helper = $helper;
    }

    /**
     * Handler for logging customer segment match
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function postDispatchCustomerSegmentMatch(Varien_Simplexml_Element $config,
        Enterprise_Logging_Model_Event $eventModel
    ) {
        $customersQty = $this->_resourceModel->getSegmentCustomersQty($this->_request->getParam('id'));
        return $eventModel->setInfo($this->_request->getParam('id') ?
            $this->_helper->__('Matched %d Customers of Segment %s', $customersQty, $this->_request->getParam('id'))
            : '-'
        );
    }
}
