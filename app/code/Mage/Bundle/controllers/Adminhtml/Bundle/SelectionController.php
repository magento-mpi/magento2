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
 * Adminhtml selection grid controller
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Adminhtml_Bundle_SelectionController extends Mage_Adminhtml_Controller_Action
{
    public function searchAction()
    {
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search')
                ->setIndex($this->getRequest()->getParam('index'))
                ->setFirstShow(true)
                ->toHtml()
           );
    }

    public function gridAction()
    {
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid',
                    'adminhtml.catalog.product.edit.tab.bundle.option.search.grid')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }

}
