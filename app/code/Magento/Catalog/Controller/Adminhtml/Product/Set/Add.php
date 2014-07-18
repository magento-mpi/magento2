<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Set;

class Add extends \Magento\Catalog\Controller\Adminhtml\Product\Set
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('New Product Template'));

        $this->_setTypeId();

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Catalog::catalog_attributes_sets');


        $this->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Toolbar\Add')
        );

        $this->_view->renderLayout();
    }
}
