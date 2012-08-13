<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreement related orders tab
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement_View_Tab_Orders extends Mage_Adminhtml_Block_Sales_Order_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Initialize grid params
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('billing_agreement_orders');
    }

    /**
     * Prepare related orders collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Grid_Collection');
        $collection->addBillingAgreementsFilter(Mage::registry('current_billing_agreement')->getId());
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Related Orders');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Related Orders');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/ordersGrid', array('_current'=>true));
    }

    /**
     * Remove import/export field from grid
     *
     * @return bool
     */
    public function getExportTypes()
    {
        return false;
    }

    /**
     * Disable massaction in grid
     *
     * @return Mage_Sales_Block_Adminhtml_Billing_Agreement_View_Tab_Orders
     */
    protected function _prepareMassaction()
    {
        return $this;
    }
}
