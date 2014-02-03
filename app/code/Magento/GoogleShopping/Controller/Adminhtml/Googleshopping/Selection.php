<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Controller\Adminhtml\Googleshopping;

/**
 * GoogleShopping Products selection grid controller
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Selection extends \Magento\Backend\App\Action
{
    /**
     * Search result grid with available products for Google Content
     *
     * @return void
     */
    public function searchAction()
    {
        $this->getResponse()->setBody(
            $this->_view->getLayout()
                ->createBlock('Magento\GoogleShopping\Block\Adminhtml\Items\Product')
                ->setIndex($this->getRequest()->getParam('index'))
                ->setFirstShow(true)
                ->toHtml()
           );
    }

    /**
     * Grid with available products for Google Content
     *
     * @return void
     */
    public function gridAction()
    {
        $this->_view->loadLayout();
        $this->getResponse()->setBody(
            $this->_view->getLayout()
                ->createBlock('Magento\GoogleShopping\Block\Adminhtml\Items\Product')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }
}
