<?php
/**
 * Adminhtml permissions user grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Permissions_User_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('permissionsUserGrid');
        $this->setDefaultSort('username');
        $this->setDefaultDir('asc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('permissions/user_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => __('ID'),
            'width'     => 5,
            'align'     => 'right',
            'sortable'  => true,
            'index'     => 'user_id'
        ));

        $this->addColumn('username', array(
            'header'    => __('User Name'),
            'index'     => 'username'
        ));

        $this->addColumn('firstname', array(
            'header'    => __('First Name'),
            'index'     => 'firstname'
        ));

        $this->addColumn('lastname', array(
            'header'    => __('Last Name'),
            'index'     => 'lastname'
        ));

        $this->addColumn('email', array(
            'header'    => __('Email'),
            'width'     => 40,
            'align'     => 'left',
            'index'     => 'email'
        ));

        $this->addColumn('is_active', array(
            'header'    => __('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array('1' => __('Active'), '0' => __('Inactive')),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('user_id' => $row->getId()));
    }

}
