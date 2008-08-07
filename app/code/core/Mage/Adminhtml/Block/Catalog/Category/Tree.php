<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Categories tree block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tree extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{

    protected $_withProductCount;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/category/tree.phtml');
        $this->setUseAjax(true);
        $this->_withProductCount = true;
    }

    protected function _prepareLayout()
    {
        if ($this->getUseAjax()) {
            $addMethod = 'addAjax';
        } else {
            $addMethod = 'add';
        }

        $url = $this->getUrl("*/*/{$addMethod}", array(
            '_current'=>true,
            'parent'=>base64_encode($this->getCategoryPath()),
            'id'=>null,
        ));

        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add Sub'),
                    'onclick'   => "addNew('".$url."')",
                    'class'     => 'add'
                ))
        );

        $this->setChild('collapse_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Collapse All'),
                    'onclick'   => 'collapseTree();',
                    'class'     => 'collapse'
                ))
        );

        $this->setChild('expand_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Expand All'),
                    'onclick'   => 'expandTree();',
                    'class'     => 'expand'
                ))
        );

        $this->setChild('store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, '_query'=>false,'store'=>null)))
        );
        return parent::_prepareLayout();
    }

    protected function _getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    public function getCategoryCollection()
    {
        $storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
        $collection = $this->getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('catalog/category')->getCollection();

            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setProductStoreId($storeId)
                ->setLoadProductCount($this->_withProductCount)
                ->setStoreId($storeId);

            $this->setData('category_collection', $collection);
        }
        return $collection;
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    public function getStoreSwitcherHtml()
    {
        if (Mage::app()->isSingleStoreMode()) {
            return '';
        }
        return $this->getChildHtml('store_switcher');
    }

    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current'=>true);
        if (
            (is_null($expanded) && Mage::getSingleton('admin/session')->getIsTreeWasExpanded())
            || $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/categoriesJson', $params);
    }

    public function getNodesUrl()
    {
        return $this->getUrl('*/catalog_category/jsonTree');
    }

    public function getEditUrl()
    {
        if ($this->getUseAjax()) {
            $editMethod = 'editAjax';
        } else {
            $editMethod = 'edit';
        }

        return $this->getUrl("*/catalog_category/{$editMethod}", array('_current'=>true, '_query'=>false, 'id'=>null, 'parent'=>null));
    }

    public function isWasExpanded()
    {
        return Mage::getSingleton('admin/session')->getIsTreeWasExpanded();
    }

    public function getMoveUrl()
    {
        return $this->getUrl('*/catalog_category/move', array('store'=>$this->getRequest()->getParam('store')));
    }

    public function getTreeJson($parenNodeCategory=null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeCategory));
        $json = Zend_Json::encode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    public function getRootIds()
    {
        $ids = $this->getData('root_ids');
        if (is_null($ids)) {
            $ids = array();
            foreach (Mage::app()->getStores() as $store) {
            	$ids[] = $store->getRootCategoryId();
            }
            $this->setData('root_ids', $ids);
        }
        return $ids;
    }

    protected function _getNodeJson($node, $level=0)
    {
        $item = array();
        $item['text']= $this->htmlEscape($node->getName());
        if ($this->_withProductCount) {
             $item['text'].= ' ('.$node->getProductCount().')';
        }

        //$rootForStores = Mage::getModel('core/store')->getCollection()->loadByCategoryIds(array($node->getEntityId()));
        $rootForStores = in_array($node->getEntityId(), $this->getRootIds());

        $item['id']  = $node->getId();
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        //$item['allowDrop'] = ($level<3) ? true : false;
        $item['allowDrop'] = true;
        // disallow drag if it's first level and category is root of a store
        $item['allowDrag'] = ($node->getLevel()==1 && $rootForStores) ? false : true;
        if ((int)$node->getChildrenCount()>0) {
            $item['children'] = array();
        }
        if ($node->hasChildren()) {
            $item['children'] = array();
            foreach ($node->getChildren() as $child) {
                $item['children'][] = $this->_getNodeJson($child, $level+1);
            }
        }

        $isParent = $this->_isParentSelectedCategory($node);

        if ($isParent || $node->getLevel()<2) {
            $item['expanded'] = true;
        }

        if ($this->getUseAjax()) {
            if ($node->getLevel() > 1 && !$isParent && isset($item['children'])) {
                $item['children'] = array();
            }
        }

        return $item;
    }

    protected function _isParentSelectedCategory($node)
    {
        if ($node) {
            $pathIds = $this->getCategory()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }

        return false;
    }
}