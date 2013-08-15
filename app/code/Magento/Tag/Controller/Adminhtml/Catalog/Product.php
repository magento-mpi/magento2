<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product tag controller
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Controller_Adminhtml_Catalog_Product extends Magento_Adminhtml_Controller_Action
{
    /**
     * Get tag grid
     */
    public function tagGridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('admin.product.tags')
            ->setProductId($this->getRequest()->getParam('id'));
        $this->renderLayout();
    }

    /**
     * Get tag customer grid
     */
    public function tagCustomerGridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('admin.product.tags.customers')
            ->setProductId($this->getRequest()->getParam('id'));
        $this->renderLayout();
    }

}
