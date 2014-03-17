<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Controller\Adminhtml\Bundle;

/**
 * Adminhtml selection grid controller
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Selection extends \Magento\Backend\App\Action
{
    /**
     * @return mixed
     */
    public function searchAction()
    {
        return $this->getResponse()->setBody(
            $this->_view->getLayout()
                ->createBlock('Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search')
                ->setIndex($this->getRequest()->getParam('index'))
                ->setFirstShow(true)
                ->toHtml()
           );
    }

    /**
     * @return mixed
     */
    public function gridAction()
    {
        return $this->getResponse()->setBody(
            $this->_view->getLayout()
                ->createBlock('Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search\Grid',
                    'adminhtml.catalog.product.edit.tab.bundle.option.search.grid')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }
}
