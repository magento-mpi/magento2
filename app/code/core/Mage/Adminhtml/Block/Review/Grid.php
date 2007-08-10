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
        $collection = Mage::getModel('review/review')
            ->getCollection();
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
            'filter_index'  => 'review.review_id',
            'index'         => 'review_id',
        ));

        $this->addColumn('created_at', array(
            'header'    => __('Created At'),
            'align'     =>'left',
            'type'      => 'datetime',
            'width'     => '100px',
            'index'     => 'created_at',
        ));

        $this->addColumn('status', array(
            'header'    => __('Status'),
            'align'     =>'left',
            'type'      => 'options',
            'options'   => $statuses,
            'width'     => '100px',
            'index'     => 'status_id',
        ));

        $this->addColumn('title', array(
            'header'    => __('Title'),
            'align'     =>'left',
            'width'     => '100px',
            'index'     => 'title',
        ));

        $this->addColumn('nickname', array(
            'header'    => __('Nickname'),
            'align'     =>'left',
            'width'     => '100px',
            'index'     => 'nickname',
        ));

        $this->addColumn('detail', array(
            'header'    => __('Review'),
            'align'     =>'left',
            'type'      => 'text',
            'index'     => 'detail',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('id' => $row->getId()));
    }
}