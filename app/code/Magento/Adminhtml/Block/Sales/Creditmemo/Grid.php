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
 * Adminhtml sales orders grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Creditmemo;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_creditmemo_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return '\Magento\Sales\Model\Resource\Order\Creditmemo\Grid\Collection';
    }

    protected function _prepareCollection()
    {
        $collection = \Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => __('Credit Memo'),
            'index'     => 'increment_id',
            'type'      => 'text',
            'header_css_class'  => 'col-memo-number',
            'column_css_class'  => 'col-memo-number'
        ));

        $this->addColumn('created_at', array(
            'header'    => __('Created'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('order_increment_id', array(
            'header'    => __('Order'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
            'header_css_class'  => 'col-order-number',
            'column_css_class'  => 'col-order-number'
        ));

        $this->addColumn('order_created_at', array(
            'header'    => __('Order Date'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('billing_name', array(
            'header' => __('Bill-to Name'),
            'index' => 'billing_name',
            'header_css_class'  => 'col-name',
            'column_css_class'  => 'col-name'
        ));

        $this->addColumn('state', array(
            'header'    => __('Status'),
            'index'     => 'state',
            'type'      => 'options',
            'options'   => \Mage::getModel('Magento\Sales\Model\Order\Creditmemo')->getStates(),
            'header_css_class'  => 'col-status',
            'column_css_class'  => 'col-status'
        ));

        $this->addColumn('grand_total', array(
            'header'    => __('Refunded'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
            'header_css_class'  => 'col-refunded',
            'column_css_class'  => 'col-refunded'
        ));

        $this->addColumn('action',
            array(
                'header'    => __('Action'),
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => __('View'),
                        'url'     => array('base'=>'*/sales_creditmemo/view'),
                        'field'   => 'creditmemo_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
                'header_css_class'  => 'col-actions',
                'column_css_class'  => 'col-actions'
        ));

        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('creditmemo_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> __('PDF Credit Memos'),
             'url'  => $this->getUrl('*/sales_creditmemo/pdfcreditmemos'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (!$this->_authorization->isAllowed(null)) {
            return false;
        }

        return $this->getUrl('*/sales_creditmemo/view',
            array(
                'creditmemo_id'=> $row->getId(),
            )
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array('_current' => true));
    }



}
