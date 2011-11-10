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
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag renderer model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * Tag data for profiler
     *
     * @var array
     */
    protected $_tags;

    /**
     * Init model
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setCount(100);
        $this->setMinAssign(1);
        $this->setMaxAssign(250);
    }

    /**
     * Render tags
     *
     * @return Mage_LoadTest_Model_Renderer_Tag
     */
    public function render()
    {
        $this->_profilerBegin();
        for ($i = 0; $i < $this->getCount(); $i ++) {
            if (!$this->_checkMemorySuffice()) {
                $urlParams = array(
                    'count='.($this->getCount() - $i),
                    'min_assign='.$this->getMinAssign(),
                    'max_assign='.$this->getMaxAssign(),
                    'detail_log='.$this->getDetailLog()
                );
                $this->_urls[] = Mage::getUrl('*/*/*/') . ' GET:"'.join(';', $urlParams).'"';
                break;
            }
            $this->_createTag();
        }
        $this->_profilerEnd();

        return $this;
    }

    /**
     * Delete all tags
     *
     * @return Mage_LoadTest_Model_Renderer_Tag
     */
    public function delete()
    {
        $this->_profilerBegin();
        $collection = Mage::getModel('Mage_Tag_Model_Tag')
            ->getCollection()
            ->load();

        foreach ($collection as $tag) {
            $this->_profilerOperationStart();
            $this->_tag = array(
                'id'    => $tag->getId(),
                'name'  => $tag->getName()
            );
            $tag->delete();
            $this->_profilerOperationStop();
        }
        $this->_profilerEnd();

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

        $this->_profilerOperationStart();

        $tag = Mage::getModel('Mage_Tag_Model_Tag');
        $tag->setName('Default Tag');
        $tag->setStatus(1);
        $tag->save();

        $tagName = 'Tag #' . $tag->getId();
        $tagId = $tag->getId();

        $tag->setName($tagName);
        $tag->save();

        for ($j = 0; $j < rand($this->getMinAssign(), $this->getMaxAssign()); $j ++) {
            $product = $this->_products[array_rand($this->_products)];
            $customer = $this->_customers[array_rand($this->_customers)];

            $tagRelation = Mage::getModel('Mage_Tag_Model_Tag_Relation');
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

        $this->_tag = array(
            'id'    => $tagId,
            'name'  => $tagName
        );
        $this->_profilerOperationStop();

        return $tagId;
    }

    /**
     * Load model data
     *
     */
    protected function _loadData()
    {
        if (is_null($this->_products)) {
            $collection = Mage::getModel('Mage_Catalog_Model_Product')
                ->getCollection()
                ->load();
            $this->_products = array();
            foreach ($collection as $product) {
                $this->_products[$product->getId()] = $product;
            }
            unset($collection);

            if (count($this->_products) == 0) {
                Mage::throwException(Mage::helper('Mage_LoadTest_Helper_Data')->__('Products not found, please create product(s) first.'));
            }
        }
        if (is_null($this->_customers)) {
            $collection = Mage::getModel('Mage_Customer_Model_Customer')
                ->getCollection()
                ->load();
            $this->_customers = array();
            foreach ($collection as $customer) {
                $this->_customers[$customer->getId()] = $customer;
            }
            if (count($this->_customers) == 0) {
                Mage::throwException(Mage::helper('Mage_LoadTest_Helper_Data')->__('Customers not found, please create customer(s) first.'));
            }
            unset($collection);
        }
    }

    protected function _profilerOperationStop()
    {
        parent::_profilerOperationStop();

        if ($this->getDebug()) {
            if (!$this->_xmlFieldSet) {
                $this->_xmlFieldSet = $this->_xmlResponse->addChild('tags');
            }

            $tag = $this->_xmlFieldSet->addChild('tag');
            $tag->addAttribute('id', $this->_tag['id']);
            $tag->addChild('name', $this->_tag['name']);
            $this->_profilerOperationAddDebugInfo($tag);
        }
    }
}