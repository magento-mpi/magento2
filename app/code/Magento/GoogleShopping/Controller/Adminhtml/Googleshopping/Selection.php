<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleShopping Products selection grid controller
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Controller_Adminhtml_Googleshopping_Selection extends Magento_Adminhtml_Controller_Action
{
    /**
     * Search result grid with available products for Google Content
     */
    public function searchAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Magento_GoogleShopping_Block_Adminhtml_Items_Product')
                ->setIndex($this->getRequest()->getParam('index'))
                ->setFirstShow(true)
                ->toHtml()
           );
    }

    /**
     * Grid with available products for Google Content
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Magento_GoogleShopping_Block_Adminhtml_Items_Product')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }
}
