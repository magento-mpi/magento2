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
 * Adminhtml selection grid controller
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Controller_Adminhtml_Bundle_Selection extends Magento_Adminhtml_Controller_Action
{
    public function searchAction()
    {
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search')
                ->setIndex($this->getRequest()->getParam('index'))
                ->setFirstShow(true)
                ->toHtml()
           );
    }

    public function gridAction()
    {
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid',
                    'adminhtml.catalog.product.edit.tab.bundle.option.search.grid')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }

}
