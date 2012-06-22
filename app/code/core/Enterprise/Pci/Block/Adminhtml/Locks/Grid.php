<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locked administrators grid
 *
 */
class Enterprise_Pci_Block_Adminhtml_Locks_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set misc grid data
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setId('lockedAdminsGrid')->setDefaultSort('user_id')->setUseAjax(true);
    }

    /**
     * Instantiate collection
     *
     * @return Mage_User_Model_Resource_User_Collection
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_collection = Mage::getResourceModel('Mage_User_Model_Resource_User_Collection');
            $this->_collection->addFieldToFilter('lock_expires', array('notnull' => true));
        }
        return $this->_collection;
    }

    /**
     * Prepare grid columns
     *
     * @return Enterprise_Pci_Block_Adminhtml_Locks_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('user_id', array(
            'header' => Mage::helper('Enterprise_Pci_Helper_Data')->__('ID'),
            'index'  => 'user_id',
            'width'  => 50,
            'filter' => false,
            'type'   => 'number'
        ));
        $this->addColumn('username', array(
            'header' => Mage::helper('Enterprise_Pci_Helper_Data')->__('Username'),
            'index'  => 'username',
        ));
        $this->addColumn('last_login', array(
            'header' => Mage::helper('Enterprise_Pci_Helper_Data')->__('Last login'),
            'index'  => 'logdate',
            'filter' => false,
            'type'   => 'datetime',
        ));
        $this->addColumn('failures_num', array(
            'header' => Mage::helper('Enterprise_Pci_Helper_Data')->__('Failures'),
            'index'  => 'failures_num',
            'filter' => false,
        ));
        $this->addColumn('lock_expires', array(
            'header'  => Mage::helper('Enterprise_Pci_Helper_Data')->__('Locked until'),
            'index'   => 'lock_expires',
            'filter'  => false,
            'type'    => 'datetime',
        ));

        $this->setDefaultFilter(array('lock_expires' => 1));

        return parent::_prepareColumns();
    }

    /**
     * Add massaction to grid
     *
     * @return Enterprise_Pci_Block_Adminhtml_Locks_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('user_id');
        $this->getMassactionBlock()->setFormFieldName('unlock');

        $this->getMassactionBlock()->addItem('unlock', array(
             'label'    => Mage::helper('Enterprise_Pci_Helper_Data')->__('Unlock'),
             'url'      => $this->getUrl('*/*/massUnlock'),
             'selected' => true,
        ));

        return $this;
    }

    /**
     * Get grid URL
     *
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid');
    }
}
