<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml bundle product edit
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Controller_Adminhtml_Bundle_Product_Edit extends Mage_Adminhtml_Controller_Catalog_Product
{
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Bundle');
    }

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
