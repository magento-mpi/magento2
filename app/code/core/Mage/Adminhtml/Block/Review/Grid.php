<?php
/**
 * Adminhtml reviews grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reviwGrid');
        $this->setDefaultSort('created_at');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $model = Mage::getModel('review/review');
        $collection = $model->getProductCollection();

        if( $this->getProductId() || $this->getRequest()->getParam('productId', false) ) {
            $this->setProductId( ( $this->getProductId() ? $this->getProductId() : $this->getRequest()->getParam('productId') ) );
            $collection->addEntityFilter($this->getProductId());
        }

        if( $this->getCustomerId() || $this->getRequest()->getParam('customerId', false) ) {
            $this->setCustomerId( ( $this->getCustomerId() ? $this->getCustomerId() : $this->getRequest()->getParam('customerId') ) );
            $collection->addCustomerFilter($this->getCustomerId());
        }

        if( Mage::registry('usePendingFilter') === true ) {
            $collection->addStatusFilter($model->getPendingStatus());
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $statuses = Mage::getModel('review/review')
            ->getStatusCollection()
            ->load()
            ->toOptionArray();

        foreach( $statuses as $key => $status ) {
            $tmpArr[$status['value']] = $status['label'];
        }

        $statuses = $tmpArr;

        $this->addColumn('review_id', array(
            'header'        => __('ID'),
            'align'         =>'right',
            'width'         => '50px',
            'filter_index'  => 'rt.review_id',
            'index'         => 'review_id',
        ));

        $this->addColumn('created_at', array(
            'header'    => __('Created At'),
            'align'     =>'left',
            'type'      => 'datetime',
            'width'     => '100px',
            'filter_index'  => 'rt.created_at',
            'index'     => 'created_at',
        ));

        if( !Mage::registry('usePendingFilter') ) {
            $this->addColumn('status', array(
                'header'    => __('Status'),
                'align'     =>'left',
                'type'      => 'options',
                'options'   => $statuses,
                'width'     => '100px',
                'filter_index'  => 'rt.status_id',
                'index'     => 'status_id',
            ));
        }

        $this->addColumn('title', array(
            'header'    => __('Title'),
            'align'     =>'left',
            'width'     => '100px',
            'filter_index'  => 'rdt.title',
            'index'     => 'title',
        ));

        $this->addColumn('nickname', array(
            'header'    => __('Nickname'),
            'align'     =>'left',
            'width'     => '100px',
            'filter_index'  => 'rdt.nickname',
            'index'     => 'nickname',
        ));

        $this->addColumn('detail', array(
            'header'    => __('Review'),
            'align'     =>'left',
            'type'      => 'text',
            'index'     => 'detail',
            'filter_index'  => 'rdt.detail',
        ));

        $this->addColumn('name', array(
            'header'    => __('Product Name'),
            'align'     =>'left',
            'type'      => 'text',
            'index'     => 'name',
        ));

        $this->addColumn('sku', array(
            'header'    => __('Product SKU'),
            'align'     => 'right',
            'type'      => 'text',
            'width'     => '50px',
            'index'     => 'sku',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/catalog_product_review/edit', array(
            'id' => $row->getReviewId(),
            'productId' => $this->getProductId(),
            'customerId' => $this->getCustomerId(),
            'ret'       => ( Mage::registry('usePendingFilter') ) ? 'pending' : null,
        ));
    }

    public function getGridUrl()
    {
        if( $this->getProductId() || $this->getCustomerId() ) {
            return Mage::getUrl('*/catalog_product_review/reviewGrid', array(
                'productId' => $this->getProductId(),
                'customerId' => $this->getCustomerId(),
            ));
        } else {
            return $this->getCurrentUrl();
        }
    }
}