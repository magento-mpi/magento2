<?php
/**
 * Google Experiment Product observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Model\Observer\Product;

class Delete
{
    /**
     * @var \Magento\GoogleOptimizer\Model\Code
     */
    protected $_modelCode;

    /**
     * @param \Magento\GoogleOptimizer\Model\Code $modelCode
     */
    public function __construct(\Magento\GoogleOptimizer\Model\Code $modelCode)
    {
        $this->_modelCode = $modelCode;
    }

    /**
     * Delete Product scripts after deleting product
     *
     * @param \Magento\Object $observer
     * @return \Magento\GoogleOptimizer\Model\Observer\Product\Delete
     */
    public function deleteProductGoogleExperimentScript($observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();
        $this->_modelCode->loadByEntityIdAndType(
            $product->getId(),
            \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_PRODUCT,
            $product->getStoreId()
        );

        if ($this->_modelCode->getId()) {
            $this->_modelCode->delete();
        }
        return $this;
    }
}
