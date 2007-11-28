<?php

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts_Customers extends Mage_Adminhtml_Block_Widget_Grid {
    
	protected $_alertModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('firstname');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setEmptyText(__('There are no customers for this alert'));
    }
    
    public function setModel(Mage_CustomerAlert_Model_Type $alertModel)
    {
        $this->_alertModel = $alertModel;
        return $this;
    }
    
    public function loadCustomers()
    {
        
        $customer = Mage::getResourceModel('customeralert/customer_collection');
        $customer -> joinField('alerts','customer_product_alert','product_id','customer_id=entity_id',$this->_alertModel->getData())
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email');
        $this->setData('customerCollection',$customer);
        return $this;
    }
    
    protected function _prepareCollection()
    {
        $customerCollection = $this->getData('customerCollection');
        $this->setCollection($customerCollection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname', array(
            'header' => __('First Name'),
            'index'  => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header' => __('Last Name'),
            'index'  => 'lastname',
        ));

        $this->addColumn('email', array(
            'header' => __('Email'),
            'index'  => 'email',
        ));

        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return Mage::getUrl('*/catalog_product/alertsGrid', $this->_alertModel->getData());
    }
}

?>
