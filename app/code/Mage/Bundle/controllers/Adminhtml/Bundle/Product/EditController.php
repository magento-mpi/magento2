<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

/**
 * Adminhtml bundle product edit
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Adminhtml_Bundle_Product_EditController extends Mage_Adminhtml_Catalog_ProductController
{
    public function formAction()
    {
        $product = $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle',
                    'admin.product.bundle.items')
                ->setProductId($product->getId())
                ->toHtml()
        );
    }
}
