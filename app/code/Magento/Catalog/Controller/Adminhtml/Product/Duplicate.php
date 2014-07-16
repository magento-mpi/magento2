<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;

class Duplicate extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * @var \Magento\Catalog\Model\Product\Copier
     */
    protected $productCopier;

    /**
     * @param Action\Context $context
     * @param Builder $productBuilder
     * @param \Magento\Catalog\Model\Product\Copier $productCopier
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Product\Builder $productBuilder,
        \Magento\Catalog\Model\Product\Copier $productCopier
    ) {
        $this->productCopier = $productCopier;
        parent::__construct($context, $productBuilder);
    }

    /**
     * Create product duplicate
     *
     * @return void
     */
    public function execute()
    {
        $product = $this->productBuilder->build($this->getRequest());
        try {
            $newProduct = $this->productCopier->copy($product);
            $this->messageManager->addSuccess(__('You duplicated the product.'));
            $this->_redirect('catalog/*/edit', array('_current' => true, 'id' => $newProduct->getId()));
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('catalog/*/edit', array('_current' => true));
        }
    }
}
