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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category abstract block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Abstract extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getCategory()
    {
        return Mage::registry('category');
    }

    public function getCategoryId()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
        }
        return Mage_Catalog_Model_Category::TREE_ROOT_ID;
    }

    public function getCategoryName()
    {
        return $this->getCategory()->getName();
    }

    public function getCategoryPath()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getPath();
        }
        return Mage_Catalog_Model_Category::TREE_ROOT_ID;
    }

    public function hasStoreRootCategory()
    {
        $root = $this->getRoot();
        if ($root && $root->getId()) {
            return true;
        }
        return false;
    }

    public function getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        return Mage::app()->getStore($storeId);
    }

    public function getRoot($parentNodeCategory=null, $recursionLevel=3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {
            $storeId = (int) $this->getRequest()->getParam('store');

            if ($storeId) {
                $store = Mage::app()->getStore($storeId);
                $rootId = $store->getRootCategoryId();
            }
            else {
                $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            }

            $tree = Mage::getResourceSingleton('catalog/category_tree')
                ->load($rootId, $recursionLevel);

            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }

            $tree->addCollectionData($this->getCategoryCollection());

            $root = $tree->getNodeById($rootId);

            if ($root && $rootId != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setIsVisible(true);
            }
            elseif($root && $root->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setName(Mage::helper('catalog')->__('Root'));
            }

            Mage::register('root', $root);
        }

        return $root;
    }

    public function getNode($parentNodeCategory, $recursionLevel=2)
    {
        $tree = Mage::getResourceModel('catalog/category_tree');

        $nodeId     = $parentNodeCategory->getId();
        $parentId   = $parentNodeCategory->getParentId();

        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);

        if ($node && $nodeId != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
            $node->setIsVisible(true);
        } elseif($node && $node->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
            $node->setName(Mage::helper('catalog')->__('Root'));
        }

        $tree->addCollectionData($this->getCategoryCollection());

        return $node;
    }

    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    public function getEditUrl()
    {
        return $this->getUrl("*/catalog_category/edit", array('_current'=>true, 'store'=>null, '_query'=>false, 'id'=>null, 'parent'=>null));
    }
}