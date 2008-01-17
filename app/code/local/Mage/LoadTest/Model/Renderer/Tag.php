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
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag renderer model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_LoadTest_Model_Renderer_Tag extends Mage_LoadTest_Model_Renderer_Abstract
{
    /**
     * Customers collection
     *
     * @var array
     */
    protected $_customers;

    /**
     * Products collection
     *
     * @var array
     */
    protected $_products;

    /**
     * Processed tags
     *
     * @var array
     */
    public $tags;

    /**
     * Init model
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setMinTags(10);
        $this->setMaxTags(20);
        $this->setMinCount(1);
        $this->setMaxCount(250);
    }

    /**
     * Render tags
     *
     * @return Mage_LoadTest_Model_Renderer_Tag
     */
    public function render()
    {
        $this->tags = array();
        for ($i = 0; $i < rand($this->getMinTags(), $this->getMaxTags()); $i ++) {
            $this->_createTag();
        }

        return $this;
    }

    /**
     * Delete all tags
     *
     * @return Mage_LoadTest_Model_Renderer_Tag
     */
    public function delete()
    {
        $this->tags = array();
        $collection = Mage::getModel('tag/tag')
            ->getCollection()
            ->load();

        foreach ($collection as $tag) {
            $this->_beforeUsedMemory();
            $this->tags[$tag->getId()] = $tag->getName();
            $tag->delete();
            $this->_afterUsedMemory();
        }

        return $this;
    }

    /**
     * Create tag
     *
     * @return int
     */
    protected function _createTag()
    {
        $this->_loadData();

        $this->_beforeUsedMemory();

        $tag = Mage::getModel('tag/tag');
        $tag->setName('Default Tag');
        $tag->setStatus(1);
        $tag->save();

        $tagName = 'Tag #' . $tag->getId();
        $tagId = $tag->getId();

        $tag->setName($tagName);
        $tag->save();

        for ($j = 0; $j < rand($this->getMinCount(), $this->getMaxCount()); $j ++) {
            $product = $this->_products[array_rand($this->_products)];
            $customer = $this->_customers[array_rand($this->_customers)];

            $tagRelation = Mage::getModel('tag/tag_relation');
            $tagRelation
                ->setTagId($tag->getId())
                ->setCustomerId($customer->getId())
                ->setStoreId($customer->getStoreId())
                ->setActive(1)
                ->setProductId($product->getId())
                ->save();

            unset($tagRelation);
        }
        $tag->aggregate();
        unset($tag);

        $this->tags[$tagId] = $tagName;
        $this->_afterUsedMemory();

        return $tagId;
    }

    /**
     * Load model data
     *
     */
    protected function _loadData()
    {
        if (is_null($this->_products)) {
            $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->load();
            $this->_products = array();
            foreach ($collection as $product) {
                $this->_products[$product->getId()] = $product;
            }
            unset($collection);

            if (count($this->_products) == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('Products not found, please create product(s) first'));
            }
        }
        if (is_null($this->_customers)) {
            $collection = Mage::getModel('customer/customer')
                ->getCollection()
                ->load();
            $this->_customers = array();
            foreach ($collection as $customer) {
                $this->_customers[$customer->getId()] = $customer;
            }
            if (count($this->_customers) == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('Customers not found, please create customer(s) first'));
            }
            unset($collection);
        }
    }
}