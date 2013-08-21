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
class Magento_Bundle_Controller_Adminhtml_Bundle_Product_Edit extends Magento_Adminhtml_Controller_Catalog_Product
{
    public function formAction()
    {
        $product = $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle',
                    'admin.product.bundle.items')
                ->setProductId($product->getId())
                ->toHtml()
        );
    }
}
