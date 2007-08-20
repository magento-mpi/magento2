<?php
/**
 * Customer's tags grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Edit_Tab_Tag extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $tagId = Mage::registry('tagId');
        $collection = Mage::getModel('tag/tag')
            ->getResourceCollection()
            ->joinRel()
            ->addCustomerFilter($this->getCustomerId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => __('Tag Name'),
            'index'     => 'name',
        ));

        return parent::_prepareColumns();
    }

    protected function getRowUrl($row)
    {
        return Mage::getUrl('*/tag/edit', array(
            'tag_id' => $row->getId(),
            'customer_id' => $this->getCustomerId(),
        ));
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/customer/tagGrid', array(
            '_current' => true,
            'id'       => $this->getCustomerId()
        ));
    }
}