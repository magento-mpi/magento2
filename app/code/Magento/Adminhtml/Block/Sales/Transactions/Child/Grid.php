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
namespace Magento\Adminhtml\Block\Sales\Transactions\Child;

class Grid extends \Magento\Adminhtml\Block\Sales\Transactions\Grid
{
    /**
     * Columns, that should be removed from grid
     *
     * @var array
     */
    protected $_columnsToRemove = array('parent_id', 'parent_txn_id');

    /**
     * Disable pager and filter
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('transactionChildGrid');
        $this->setDefaultSort('created_at');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Add filter by parent transaction ID
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getResourceModel('\Magento\Sales\Model\Resource\Order\Payment\Transaction\Collection');
        $collection->addParentIdFilter(\Mage::registry('current_transaction')->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Remove some columns and make other not sortable
     *
     */
    protected function _prepareColumns()
    {
        $result = parent::_prepareColumns();

        foreach ($this->_columns as $key => $value) {
            if (in_array($key, $this->_columnsToRemove)) {
                unset($this->_columns[$key]);
            } else {
                $this->_columns[$key]->setData('sortable', false);
            }
        }
        return $result;
    }
}
