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
        #$this->setTemplate('tag/product/result.phtml');

        $this->setTemplate('catalog/search/result.phtml');
        $this->setTagId(Mage::registry('tagId'));
    }

    public function getTagInfo()
    {
        return Mage::getModel('tag/tag')->load($this->getTagId());
    }

    protected function _initChildren()
    {
        $title = $this->getHeaderText();
        $this->getLayout()->getBlock('head')->setTitle($title);
        $this->getLayout()->getBlock('root')->setHeaderTitle($title);

        $resultBlock = $this->getLayout()->createBlock('catalog/product_list', 'product_list')
            ->setAvailableOrders(array('name'=>__('Name'), 'price'=>__('Price')))
            ->setModes(array('list' => __('List'), 'grid' => __('Grid')))
            ->setCollection($this->_getCollection());
        $this->setChild('search_result_list', $resultBlock);
    }

    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    protected function _getCollection()
    {
        if( !$this->_collection ) {
            $tagModel = Mage::getModel('tag/tag');
            $this->_collection = $tagModel->getEntityCollection()
                ->addTagFilter($this->getTagId());
        }
        return $this->_collection;
    }

    public function _getProductCollection()
    {
        return $this->_getCollection();
    }

    protected function _beforeToHtml()
    {
        Mage::getModel('review/review')->appendSummary($this->_getCollection());
        return parent::_beforeToHtml();
    }

    public function getResultCount()
    {
    	return $this->_getProductCollection()->getSize();
    }

    public function getHeaderText()
    {
        if( $this->getTagInfo()->getName() ) {
            return __('Products tagged with \'%s\'', $this->getTagInfo()->getName());
        } else {
            return false;
        }
    }

    public function getSubheaderText()
    {
        return false;
    }

    public function getNoResultText()
    {
        return __('No mathces found.');
    }
}