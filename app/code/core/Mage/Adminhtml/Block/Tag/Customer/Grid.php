<?php
/**
 * Child Of Mage_Adminhtml_Block_Tag_Customer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tag_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_grid' . Mage::registry('tagId'));
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $tagId = Mage::registry('tagId');
        $collection = Mage::getModel('tag/tag')
            ->getCustomerCollection()
            ->addTagFilter($tagId);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_id', array(
            'header'        => __('ID'),
            'width'         => '50px',
            'align'         => 'right',
            'index'         => 'entity_id',
        ));

        $this->addColumn('firstname', array(
            'header'    => __('Customer Name'),
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'    => __('Customer Name'),
            'index'     => 'lastname',
        ));

        return parent::_prepareColumns();
    }

    protected function getRowUrl($row)
    {
        return Mage::getUrl('*/customer/edit', array('id' => $row->getCustomerId()));
    }
}