<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile related orders grid
 */
class Magento_Sales_Block_Recurring_Profile_Related_Orders_Grid extends Magento_Sales_Block_Recurring_Profile_View
{
    /**
     * Prepare related orders collection
     *
     * @param array|string $fieldsToSelect
     */
    protected function _prepareRelatedOrders($fieldsToSelect = '*')
    {
        if (null === $this->_relatedOrders) {
            $this->_relatedOrders = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Collection')
                ->addFieldToSelect($fieldsToSelect)
                ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId())
                ->addRecurringProfilesFilter($this->_profile->getId())
                ->setOrder('entity_id', 'desc');
        }
    }

    /**
     * Prepare related grid data
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_prepareRelatedOrders(array(
            'increment_id', 'created_at', 'customer_firstname', 'customer_lastname', 'base_grand_total', 'status'
        ));
        $this->_relatedOrders->addFieldToFilter('state', array(
            'in' => Mage::getSingleton('Magento_Sales_Model_Order_Config')->getVisibleOnFrontStates()
        ));

        $pager = $this->getLayout()->createBlock('Magento_Page_Block_Html_Pager')
            ->setCollection($this->_relatedOrders)->setIsOutputRequired(false);
        $this->setChild('pager', $pager);

        $this->setGridColumns(array(
            new Magento_Object(array(
                'index' => 'increment_id',
                'title' => $this->__('Order #'),
                'is_nobr' => true,
                'width' => 1,
            )),
            new Magento_Object(array(
                'index' => 'created_at',
                'title' => $this->__('Date'),
                'is_nobr' => true,
                'width' => 1,
            )),
            new Magento_Object(array(
                'index' => 'customer_name',
                'title' => $this->__('Customer Name'),
            )),
            new Magento_Object(array(
                'index' => 'base_grand_total',
                'title' => $this->__('Order Total'),
                'is_nobr' => true,
                'width' => 1,
                'is_amount' => true,
            )),
            new Magento_Object(array(
                'index' => 'status',
                'title' => $this->__('Order Status'),
                'is_nobr' => true,
                'width' => 1,
            )),
        ));

        $orders = array();
        foreach ($this->_relatedOrders as $order) {
            $orders[] = new Magento_Object(array(
                'increment_id' => $order->getIncrementId(),
                'created_at' => $this->formatDate($order->getCreatedAt()),
                'customer_name' => $order->getCustomerName(),
                'base_grand_total' => Mage::helper('Magento_Core_Helper_Data')->formatCurrency(
                    $order->getBaseGrandTotal(), false
                ),
                'status' => $order->getStatusLabel(),
                'increment_id_link_url' => $this->getUrl('sales/order/view/', array('order_id' => $order->getId())),
            ));
        }
        if ($orders) {
            $this->setGridElements($orders);
        }
    }
}
