<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Controller\Adminhtml;

class Edit extends \Magento\Backend\App\AbstractAction
{
    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $factory;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $factory
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $factory,
        \Magento\Logger $logger
    ) {
        $this->registry = $registry;
        $this->factory = $factory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::products');
    }

    /**
     * Get associated grouped products grid popup
     */
    public function popupAction()
    {
        $productId  = (int)$this->getRequest()->getParam('id');

        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->factory->create();
        $product->setStoreId($this->getRequest()->getParam('store', 0));

        $typeId = $this->getRequest()->getParam('type');
        if (!$productId && $typeId) {
            $product->setTypeId($typeId);
        }
        $product->setData('_edit_mode', true);

        if ($productId) {
            try {
                $product->load($productId);
            } catch (\Exception $e) {
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE);
                $this->logger->logException($e);
            }
        }

        $setId = (int)$this->getRequest()->getParam('set');
        if ($setId) {
            $product->setAttributeSetId($setId);
        }
        $this->registry->register('current_product', $product);

        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
} 
