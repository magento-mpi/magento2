<?php
/**
 * description
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Promo_Catalog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('promo_catalog_grid');
        $this->setDefaultSort('rule_title');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalogrule/rule')
            ->getResourceCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => __('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'poll_id',
        ));

        $this->addColumn('rule_name', array(
            'header'    => __('Poll Name'),
            'align'     =>'left',
            'index'     => 'poll_title',
        ));

        $this->addColumn('from_date', array(
            'header'    => __('Date Posted'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'date_posted',
        ));

        $this->addColumn('to_date', array(
            'header'    => __('Date Closed'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'date_closed',
        ));

        $this->addColumn('is_active', array(
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
        
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('id' => $row->getId()));
    }
}