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
 * Class \Magento\CustomerSegment\Model\Logging
 *
 * Model for logging event related to Customer Segment, active only if Magento_Logging module is enabled
 */
namespace Magento\CustomerSegment\Model;

class Logging
{
    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment|null
     */
    protected $_resourceModel = null;

    /**
     * @var \Magento\App\RequestInterface|null
     */
    protected $_request = null;

    /**
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceModel
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(
        \Magento\CustomerSegment\Model\Resource\Segment $resourceModel,
        \Magento\App\RequestInterface $request
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_request = $request;
    }

    /**
     * Handler for logging customer segment match
     *
     * @param array $config
     * @param \Magento\Logging\Model\Event $eventModel
     * @return \Magento\Logging\Model\Event
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function postDispatchCustomerSegmentMatch($config,
        \Magento\Logging\Model\Event $eventModel
    ) {
        $segmentId = $this->_request->getParam('id');
        $customersQty = $this->_resourceModel->getSegmentCustomersQty($segmentId);
        return $eventModel->setInfo($segmentId ?
            __('Matched %1 Customers of Segment %2', $customersQty, $segmentId)
            : '-'
        );
    }
}
