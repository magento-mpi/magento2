<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Controller\Adminhtml\Bundle\Product;

/**
 * Adminhtml bundle product edit
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * @return void
     */
    public function formAction()
    {
        $product = $this->_initProduct();
        $this->getResponse()->setBody(
            $this->_view->getLayout()
                ->createBlock('Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle',
                    'admin.product.bundle.items')
                ->setProductId($product->getId())
                ->toHtml()
        );
    }
}
