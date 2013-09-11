<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml bundle product edit
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Controller\Adminhtml\Bundle\Product;

class Edit extends \Magento\Adminhtml\Controller\Catalog\Product
{
    public function formAction()
    {
        $product = $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('\Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle',
                    'admin.product.bundle.items')
                ->setProductId($product->getId())
                ->toHtml()
        );
    }
}
