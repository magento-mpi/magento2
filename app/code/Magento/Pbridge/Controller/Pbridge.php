<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Index controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Controller;

class Pbridge extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Logger $logger
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Load only action layout handles
     *
     * @return $this
     */
    protected function _initActionLayout()
    {
        $this->_view->addActionLayoutHandles();
        $this->_view->loadLayoutUpdates();
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();
        $this->_view->setIsLayoutLoaded(true);
        return $this;
    }
}
