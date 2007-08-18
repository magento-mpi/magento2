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

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
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
        $this->setChild('pager',
            $this->getLayout()->createBlock('page/html_pager', 'review_pager')
                        ->setCollection($this->_getCollection())
                        ->setUrlPrefix('tag')
                        ->setViewBy('limit')
                        ->setViewBy('mode', array('grid', 'list'))
                        ->setViewBy('order', array('name', 'price'))
        );
    }

    protected function _getCollection()
    {
        if( !$this->_collection ) {
            $tagModel = Mage::getModel('tag/tag');

            $this->_collection = $tagModel->getEntityCollection();

            $this->_collection
                ->addStoreFilter(Mage::getSingleton('core/store')->getId())
                ->addTagFilter($this->getTagId())
                ->addStatusFilter($tagModel->getApprovedStatus());
        }
        return $this->_collection;
    }
}