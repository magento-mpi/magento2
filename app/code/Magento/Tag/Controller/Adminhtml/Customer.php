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
 * Controller to process customer tag actions
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Controller_Adminhtml_Customer extends Magento_Adminhtml_Controller_Action
{
    /**
     * Adds to registry current customer instance
     *
     * @param string $idFieldName
     * @return Magento_Tag_Controller_Adminhtml_Customer
     */
    protected function _initCustomer($idFieldName = 'id')
    {
        $this->_title(__('Customers'))->_title(__('Customers'));

        $customerId = (int) $this->getRequest()->getParam($idFieldName);
        $customer   = Mage::getModel('Magento_Customer_Model_Customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

    /**
     * Processes ajax action to render tags tab content
     */
    public function productTagsAction()
    {
        $this->_initCustomer();

        /** @var $customer Magento_Customer_Model_Customer */
        $customer = Mage::registry('current_customer');
        /** @var $block Magento_Tag_Block_Adminhtml_Customer_Edit_Tab_Tag_Grid */
        $block = $this->loadLayout()
            ->getLayout()
            ->getBlock('admin.customer.tags');
        $block->setCustomerId($customer->getId())
            ->setUseAjax(true);

        $this->renderLayout();
    }

    /**
     * Processes tag grid actions
     */
    public function tagGridAction()
    {
        $this->_initCustomer();

        /** @var $block Magento_Tag_Block_Adminhtml_Customer_Edit_Tab_Tag_Grid */
        $block = $this->loadLayout()
            ->getLayout()
            ->getBlock('admin.customer.tags');
        $block->setCustomerId(Mage::registry('current_customer'));

        $this->renderLayout();
    }
}
