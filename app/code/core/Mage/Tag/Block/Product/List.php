<?php
class Mage_Tag_Block_Product_List extends Mage_Core_Block_Template
{
	protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tag/list.phtml');
    }

    public function getCount()
    {
        return count($this->getTags());
    }

    public function getTags()
    {
        return $this->_getCollection()->getItems();
    }

    public function getFormAction()
    {
        return Mage::getUrl('tag/index/save', array('productId' => $this->_getProductId()));
    }

    protected function _getCollection()
    {
        if( !$this->_collection ) {
            $model = Mage::getModel('tag/tag');
            $this->_collection = $model->getResourceCollection()
                ->addStoreFilter(Mage::getSingleton('core/store')->getId())
                ->addStatusFilter($model->getApprovedStatus())
                ->addPopularity()
                ->addProductFilter($this->_getProductId())
                ->load();
        }
        return $this->_collection;
    }

    protected function _getProductId()
    {
        return Mage::registry('controller')->getRequest()->getParam('id', false);
    }
}