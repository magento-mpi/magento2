<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Customer segments grid and edit controller
 */
class Index extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\CustomerSegment\Model\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_conditionFactory = $conditionFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Initialize proper segment model
     *
     * @param string $requestParam
     * @param bool $requireValidId
     * @return \Magento\CustomerSegment\Model\Segment
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _initSegment($requestParam = 'id', $requireValidId = false)
    {
        $segmentId = $this->getRequest()->getParam($requestParam, 0);
        $segment = $this->_objectManager->create('Magento\CustomerSegment\Model\Segment');
        if ($segmentId || $requireValidId) {
            $segment->load($segmentId);
            if (!$segment->getId()) {
                throw new \Magento\Framework\Model\Exception(__('You requested the wrong customer segment.'));
            }
        }
        $this->_coreRegistry->register('current_customer_segment', $segment);
        return $segment;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Magento_CustomerSegment::customersegment'
        ) && $this->_objectManager->get(
            'Magento\CustomerSegment\Helper\Data'
        )->isEnabled();
    }
}
