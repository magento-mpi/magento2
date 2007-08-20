<?php
/**
 * List of tagged products
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_Block_Product_Result extends Mage_Core_Block_Template
{
    protected $_collection;

    public function __construct()
    {
        $this->setTemplate('tag/product/result.phtml');
        $this->setTagId(Mage::registry('tagId'));
    }

    public function getTagInfo()
    {
        return Mage::getModel('tag/tag')->load($this->getTagId());
    }

    public function getProducts()
    {
        return $this->_getCollection()->getItems();
    }

    public function getCount()
    {
        return sizeof($this->getProducts());
    }

    protected function _initChildren()
    {
        $toolbar = $this->getLayout()->createBlock('catalog/product_list_toolbar', 'tag_list.toolbar')
            ->setCollection($this->_getCollection());

        $this->setChild('toolbar', $toolbar);
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    protected function _getCollection()
    {
        if( !$this->_collection ) {
            $tagModel = Mage::getModel('tag/tag');

            $this->_collection = $tagModel->getEntityCollection();

            $this->_collection
                ->addStoreFilter(Mage::getSingleton('core/store')->getId())
                ->addTagFilter($this->getTagId())
                #->addStatusFilter($tagModel->getApprovedStatus())
                ;
            Mage::getModel('review/review')->appendSummary($this->_collection);
        }
        return $this->_collection;
    }

    protected function _beforeToHtml()
    {
        $this->_getCollection()->load();
        Mage::getModel('review/review')->appendSummary($this->_getCollection());
        return parent::_beforeToHtml();
    }
}