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
 * Adminhtml customer billing agreement tab
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Customer_Edit_Tab_Agreement
    extends Mage_Sales_Block_Adminhtml_Billing_Agreement_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Columns, that should be removed from grid
     *
     * @var array
     */
    protected $_columnsToRemove = array('customer_email', 'customer_firstname', 'customer_lastname');

    /**
     * Disable filters and paging
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_edit_tab_agreements');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Billing Agreements');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Billing Agreements');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return (bool)$customer->getId();
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

    public function getGridUrl()
    {
        return $this->getUrl('*/sales_billing_agreement/customerGrid', array('_current'=>true));
    }

    /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'orders';
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Sales_Block_Adminhtml_Customer_Edit_Tab_Agreement
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Billing_Agreement_Collection')
            ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId())
            ->setOrder('created_at');
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * Remove some columns and make other not sortable
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $result = parent::_prepareColumns();

        foreach ($this->_columns as $key => $value) {
            if (in_array($key, $this->_columnsToRemove)) {
                unset($this->_columns[$key]);
            }
        }
        return $result;
    }
}
