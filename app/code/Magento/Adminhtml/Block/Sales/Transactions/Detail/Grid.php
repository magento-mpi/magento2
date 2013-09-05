<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml transaction details grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Transactions_Detail_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize default sorting and html ID
     */
    protected function _construct()
    {
        $this->setId('transactionDetailsGrid');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Prepare collection for grid
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = new \Magento\Data\Collection();
        foreach ($this->getTransactionAdditionalInfo() as $key => $value) {
            $data = new \Magento\Object(array('key' => $key, 'value' => $value));
            $collection->addItem($data);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('key', array(
            'header'    => __('Key'),
            'index'     => 'key',
            'sortable'  => false,
            'type'      => 'text',
            'header_css_class'  => 'col-key',
            'column_css_class'  => 'col-key'
        ));

        $this->addColumn('value', array(
            'header'    => __('Value'),
            'index'     => 'value',
            'sortable'  => false,
            'type'      => 'text',
            'escape'    => true,
            'header_css_class'  => 'col-value',
            'column_css_class'  => 'col-value'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve Transaction addtitional info
     *
     * @return array
     */
    public function getTransactionAdditionalInfo()
    {
        $info = Mage::registry('current_transaction')->getAdditionalInformation(
            Magento_Sales_Model_Order_Payment_Transaction::RAW_DETAILS
        );
        return (is_array($info)) ? $info : array();
    }
}
