<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RSS Controller for Order feed
 */
namespace Magento\Rss\Controller;

class Order extends \Magento\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Order status action
     */
    public function statusAction()
    {
        $order = $this->_objectManager->get('Magento\Rss\Helper\Order')
            ->getOrderByStatusUrlKey((string)$this->getRequest()->getParam('data'));

        if (!is_null($order)) {
            $this->_coreRegistry->register('current_order', $order);
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->_view->loadLayout(false);
            $this->_view->renderLayout();
            return;
        }

        $this->_forward('nofeed', 'index', 'rss');
    }
}
