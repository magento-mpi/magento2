<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Rma\Model\Rma as RmaModel;

class Rma extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Application filesystem
     *
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    /**
     * Read directory
     *
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $readDirectory;

    /**
     * Http response file factory
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * Shipping carrier helper
     *
     * @var \Magento\Shipping\Helper\Carrier
     */
    protected $carrierHelper;

    /**
     * @var \Magento\Rma\Model\Shipping\LabelService
     */
    protected $labelService;

    /**
     * @var \Magento\Rma\Model\Rma\RmaDataMapper
     */
    protected $rmaDataMapper;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Shipping\Helper\Carrier $carrierHelper
     * @param \Magento\Rma\Model\Shipping\LabelService $labelService
     * @param RmaModel\RmaDataMapper $rmaDataMapper
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Shipping\Helper\Carrier $carrierHelper,
        \Magento\Rma\Model\Shipping\LabelService $labelService,
        \Magento\Rma\Model\Rma\RmaDataMapper $rmaDataMapper
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->readDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->_fileFactory = $fileFactory;
        $this->carrierHelper = $carrierHelper;
        $this->labelService = $labelService;
        $this->rmaDataMapper = $rmaDataMapper;
        parent::__construct($context);
    }

    /**
     * Init active menu and set breadcrumb
     *
     * @return \Magento\Rma\Controller\Adminhtml\Rma
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Rma::sales_magento_rma_rma');

        $this->_title->add(__('Returns'));
        return $this;
    }

    /**
     * Initialize model
     *
     * @param string $requestParam
     * @return \Magento\Rma\Model\Rma
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _initModel($requestParam = 'id')
    {
        /** @var $model \Magento\Rma\Model\Rma */
        $model = $this->_objectManager->create('Magento\Rma\Model\Rma');
        $model->setStoreId($this->getRequest()->getParam('store', 0));

        $rmaId = $this->getRequest()->getParam($requestParam);
        if ($rmaId) {
            $model->load($rmaId);
            if (!$model->getId()) {
                throw new \Magento\Framework\Model\Exception(__('The wrong RMA was requested.'));
            }
            $this->_coreRegistry->register('current_rma', $model);
            $orderId = $model->getOrderId();
        } else {
            $orderId = $this->getRequest()->getParam('order_id');
        }

        if ($orderId) {
            /** @var $order \Magento\Sales\Model\Order */
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            if (!$order->getId()) {
                throw new \Magento\Framework\Model\Exception(__('This is the wrong RMA order ID.'));
            }
            $this->_coreRegistry->register('current_order', $order);
        }

        return $model;
    }

    /**
     * Initialize model
     *
     * @return \Magento\Rma\Model\Rma\Create
     */
    protected function _initCreateModel()
    {
        /** @var $model \Magento\Rma\Model\Rma\Create */
        $model = $this->_objectManager->create('Magento\Rma\Model\Rma\Create');
        $orderId = $this->getRequest()->getParam('order_id');
        $model->setOrderId($orderId);
        if ($orderId) {
            /** @var $order \Magento\Sales\Model\Order */
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            $model->setCustomerId($order->getCustomerId());
            $model->setStoreId($order->getStoreId());
        }
        $this->_coreRegistry->register('rma_create_model', $model);
        return $model;
    }

    /**
     * Check the permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Rma::magento_rma');
    }
}
