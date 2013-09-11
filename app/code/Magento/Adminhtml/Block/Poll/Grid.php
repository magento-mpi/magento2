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
 * description
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Poll;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('pollGrid');
        $this->setDefaultSort('poll_title');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = \Mage::getModel('Magento\Poll\Model\Poll')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();

        if (!\Mage::app()->isSingleStoreMode()) {
            $this->getCollection()->addStoreData();
        }

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('poll_id', array(
            'header'    => __('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'poll_id',
        ));

        $this->addColumn('poll_title', array(
            'header'    => __('Poll Question'),
            'align'     =>'left',
            'index'     => 'poll_title',
        ));

        $this->addColumn('votes_count', array(
            'header'    => __('Responses'),
            'width'     => '50px',
            'type'      => 'number',
            'index'     => 'votes_count',
        ));

        $this->addColumn('date_posted', array(
            'header'    => __('Posted'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'datetime',
            'index'     => 'date_posted',
            'date_format' => \Mage::app()->getLocale()->getDateFormat()
        ));

        $this->addColumn('date_closed', array(
            'header'    => __('Closed'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'date_closed',
            'date_format' => \Mage::app()->getLocale()->getDateFormat()
        ));

        if (!\Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', array(
                'header'    => __('Visibility'),
                'index'     => 'stores',
                'type'      => 'store',
                'store_view' => true,
                'sortable'   => false,
            ));
        }

        /*
        $this->addColumn('active', array(
            'header'    => __('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));
        */
        $this->addColumn('closed', array(
            'header'    => __('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'closed',
            'type'      => 'options',
            'options'   => array(
                1 => __('Closed'),
                0 => __('Open')
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
