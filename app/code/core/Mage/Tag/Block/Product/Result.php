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

    protected function _initChildren()
    {
        $list = $this->getLayout()->createBlock('page/html_pager', 'tag_product_list')
            ->setCollection($this->_getCollection());
        $this->setChild('list', $list);
    }

    public function getListHtml()
    {
        return $this->getChildHtml('list');
    }

    protected function _getCollection()
    {
        if( !$this->_collection ) {
            $tagModel = Mage::getModel('tag/tag');
            $this->_collection = $tagModel->getEntityCollection()
                ->addTagFilter($this->getTagId())
                ->load();
        }
        return $this->_collection;
    }

    protected function _beforeToHtml()
    {
        Mage::getModel('review/review')->appendSummary($this->_getCollection());
        return parent::_beforeToHtml();
    }

    public function getCount()
    {
        return sizeof($this->_getCollection()->getItems());
    }

    public function getProducts()
    {
        return $this->_getCollection()->getItems();
    }
}