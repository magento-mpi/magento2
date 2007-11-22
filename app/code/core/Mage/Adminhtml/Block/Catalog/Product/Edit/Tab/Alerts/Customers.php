<?php

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts_Customers  extends Mage_Adminhtml_Block_Widget_Grid {
    
	public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('firstname');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setEmptyText(__('There are no customers for this alert'));
    }
    
    public function loadCustomers()
    {
         $customerIds = Mage::getModel(Mage::getConfig()->getNode('global/customeralert/types/'.$this->getId().'/model'))
                         ->setProductId($this->getProductId())
                         ->setStoreId($this->getStore())
                         ->loadCustomersId();
         $this->setData('customerIds',$customerIds);
         $this->_prepareCollection();
         return $this;
    }
    
    protected function _prepareCollection()
    {
        $customerIds = $this->getData('customerIds');
       # Mage_Customer_Model_Entity_Customer_Collection
        if(count($customerIds)>0){
            $collection = Mage::getResourceModel('customer/customer_collection')
                        ->addAttributeToFilter('entity_id',$customerIds)
                        ->addAttributeToSelect('firstname')
                        ->addAttributeToSelect('lastname')
                        ->addAttributeToSelect('email')
                        ->load();
            $this->setCollection($collection);
        }
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname', array(
            'header'    => __('First Name'),
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'        => __('Last Name'),
            'index'         => 'lastname',
        ));

        $this->addColumn('email', array(
            'header'        => __('Email'),
            'index'         => 'email',
        ));

        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return Mage::getUrl('*/catalog_product/alertsGrid', array(
            '_current' => true,
            'id'       => $this->getId(),
            'productId' => $this->getData('productId'),
            'store' => $this->getData('store'),
        ));
    }

}

?>
