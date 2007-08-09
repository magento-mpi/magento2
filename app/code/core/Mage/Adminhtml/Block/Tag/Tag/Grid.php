<?php
/**
 * Adminhtml all tags grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Tag_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tagAllGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tag/tag_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = Mage::getUrl();

        $this->addColumn('name', array(
            'header'    => __('Tag'),
            'index'     => 'name',
        ));

        $this->addColumn('total_used', array(
            'header'    => __('# of Uses'),
            'width'     => '140px',
            'align'     => 'center',
            'index'     => 'total_used',
            'type'      => 'number',
        ));
        $this->addColumn('status', array(
            'header'    => __('Status'),
            'width'     => '90px',
            'index'     => 'status',
            'type'      => 'options',
            'options'    => array(
                Mage_Tag_Model_Tag::STATUS_DISABLED => __('Disabled'),
                Mage_Tag_Model_Tag::STATUS_PENDING  => __('Pending'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => __('Approved'),
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('tag_id' => $row->getId()));
    }

}
