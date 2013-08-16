<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product tag controller
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Controller_Adminhtml_Catalog_Product extends Mage_Adminhtml_Controller_Action
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
