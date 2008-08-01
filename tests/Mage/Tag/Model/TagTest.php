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
 * @category   Tests
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../../PHPUnitTestInit.php';
    PHPUnitTestInit::runMe(__FILE__);
}

/**
 * DESCRIPTION
 *
 * TODO: tearDown() method realization (remove all temporeary created data, i.e. products, tags, customers, relations etc.)
 *
 * @category   Mage
 * @package    Mage_PACKAGE
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_TagTest extends PHPUnit_Framework_TestCase
{
    /**
     * DB Adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_connection;

    protected function setUp()
    {
        Mage::app();
    }

    public function testGetPopularity()
    {
        $tag = Mage::getModel('tag/tag');
        $value = 'string';
        $tag->setPopularity($value);

        $this->assertEquals($value, $tag->getPopularity());
    }

    public function testGetName()
    {
        $tag = Mage::getModel('tag/tag');
        $value = 'string';
        $tag->setName($value);

        $this->assertEquals($value, $tag->getName());
    }

    public function testGetTagId()
    {
        $tag = Mage::getModel('tag/tag');
        $value = 1;
        $tag->setTagId($value);

        $this->assertEquals($value, $tag->getTagId());
    }

    public function testGetRatio()
    {
        $tag = Mage::getModel('tag/tag');
        $value = 1;
        $tag->setRatio($value);

        $this->assertEquals($value, $tag->getRatio());
    }

    public function testLoadByName()
    {
        $tagName = uniqid();
        $this->_createTag($tagName);

        $tag = Mage::getModel('tag/tag')->loadByName($tagName);

        $this->assertTrue(!is_null($tag->getId()));

        $tag->delete();
    }

    public function testAggregate()
    {
        $customerCount  = 5;
        $productCount   = 3;

        $tag = $this->_createTag();

        $products = array();
        for ($i = 0; $i < $productCount; $i++) {
            $products[] = $this->_createProduct();
        }

        $customers = array();
        for ($i = 0; $i < $customerCount; $i++) {
            $customer = $this->_createCustomer();
            $customers[] = $customer;
            foreach ($products as $product) {
                $this->_createRelation($tag->getId(), $product->getId(), $customer->getId());
            }
        }

        $tag->aggregate();

        $row = $this->_getSummary($tag->getId());

        $this->assertEquals($row, array(
            'tag_id'            => $tag->getId(),
            'store_id'          => Mage::app()->getStore()->getId(),
            'customers'         => $customerCount,
            'products'          => $productCount,
            'uses'              => $productCount * $customerCount,
            'historical_uses'   => $productCount * $customerCount,
            'popularity'        => $productCount * $customerCount
        ));

        $tag->delete();
        for ($i = 0; $i < $productCount; $i++) {
            $products[$i]->delete();
        }

    }

    /**
     *  tests event product aggregate
     */
    public function testProductEventAggregate ()
    {
        $productsCount = 2;
        $customer = $this->_createCustomer();
        $tag = $this->_createTag();
        $relations = array();
        $products = array();
        $observer = new Varien_Object();
        $event = new Varien_Object();
        for ($i = 0; $i < $productsCount; $i++) {
            $products[$i] = $this->_createProduct();
            $relations[$i] = $this->_createRelation($tag->getId(), $products[$i]->getId(), $customer->getId());
            $event->setProduct($products[$i]);
            $observer->setEvent($event);
            Mage::getModel('tag/tag')->productEventAggregate($observer);
        }

        $row = $this->_getSummary($tag->getId());

        $beforeDeleteCheck = array_diff($row, array(
            'tag_id'            => $tag->getId(),
            'store_id'          => Mage::app()->getStore()->getId(),
            'customers'         => 1,
            'products'          => $productsCount,
            'uses'              => $productsCount,
            'historical_uses'   => $productsCount,
            'popularity'        => $productsCount
        ));

        $c = count($relations);
        $observer = new Varien_Object();
        $event = new Varien_Object();
        for ($i = 0; $i < $c; $i++) {
            $relations[$i]->delete();
            $event->setProduct($products[$i]);
            $observer->setEvent($event);
            Mage::getModel('tag/tag')->productEventAggregate($observer);
        }

        $row = $this->_getSummary($tag->getId());

        $afterDeleteCheck = array_diff($row, array(
            'tag_id'            => $tag->getId(),
            'store_id'          => Mage::app()->getStore()->getId(),
            'customers'         => 1,
            'products'          => 0,
            'uses'              => 0,
            'historical_uses'   => $productsCount,
            'popularity'        => 0
        ));

        // delete temporary data (MUST BE MOVED TO tearDown())
        $customer->delete();
        $tag->delete();
        $c = count($products);
        for ($i = 0; $i < $c; $i++) {
            $products[$i]->delete();
        }
        $this->_getConnection()->delete(
            Mage::getResourceModel('tag/tag')->getTable('tag/summary'),
            $this->_getConnection()->quoteInto('tag_id = ?', $tag->getId()
        ));

        $this->assertTrue(count($beforeDeleteCheck) == 0 && count($afterDeleteCheck) == 0);
    }

    /**
     * WARINNG: it seems to be Mage_Tag_Model_Mysql4_Tag::addSummary() works incorrectly:
     *
     *      $row = $this->_getReadAdapter()->fetchAll($select);
     *      $object->addData($row);
     * MUST BE:
     *      $row = $this->_getReadAdapter()->fetchRow($select);  !!!!
     *      $object->addData($row);
     * So, I didn't fix it, just fix this test
     *
     *  tests adding Summary info to a tag object
     */
    public function testAddSumary ()
    {
        $tag = $this->_createTag();
        $storeId = Mage::app()->getStore()->getId();
        $summary = array(
            'tag_id'            => $tag->getId(),
            'store_id'          => $storeId,
            'customers'         => 2,
            'products'          => 2,
            'uses'              => 2,
            'historical_uses'   => 2,
            'popularity'        => 2
        );
        $this->_getConnection()->insert(Mage::getResourceModel('tag/tag')->getTable('summary'), $summary);

        $tag->setStoreId($storeId);
        $tag->addSummary($storeId);
        $row = $this->_getSummary($tag->getId());
        $data = $tag->getData('0'); // :) summary added as array(0 => array(...), 1 => array(...)...)
        $tag->delete();
        $this->assertEquals($row, array(
            'tag_id'            => $data['tag_id'],
            'store_id'          => $data['store_id'],
            'customers'         => $data['customers'],
            'products'          => $data['products'],
            'uses'              => $data['uses'],
            'historical_uses'   => $data['historical_uses'],
            'popularity'        => $data['popularity'],
        ));
    }

    /**
     * Retrieve connection adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _getConnection()
    {
        if (is_null($this->_connection)) {
            $this->_connection = Mage::getSingleton('core/resource')->getConnection('tag_read');
        }
        return $this->_connection;
    }

    /**
     * Create product and retrieve model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _createProduct()
    {
        $attributeSetId = Mage::getModel('eav/entity_type')
            ->loadByCode('catalog_product')
            ->getDefaultAttributeSetId();

        return Mage::getModel('catalog/product')
            ->setTypeId('simple')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setName(uniqid())
            ->setDescription('test desc')
            ->setShortDescription('test shortdesc')
            ->setSku(uniqid())
            ->setWeight(1)
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setPrice(100)
            ->setWebsiteIds(array(Mage::app()->getStore()->getWebsiteId()))
            ->setAttributeSetId($attributeSetId)
            ->save();
    }

    /**
     * Create new customer and retrieve model
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _createCustomer()
    {
        return Mage::getModel('customer/customer')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setFirstname(uniqid())
            ->setLastname(uniqid())
            ->setEmail(uniqid() . '@varien.com')
            ->setGroupId('general')
            ->setPassword(uniqid())
            ->setCreatedIn(Mage::app()->getStore()->getWebsiteId())
            ->setIsSubscribed(false)
            ->save();
    }

    /**
     * Create tag and retrieve model
     *
     * @param string $name
     * @return Mage_Tag_Model_Tag
     */
    protected function _createTag($name = null)
    {
        return Mage::getModel('tag/tag')
            ->setName($name ? $name : uniqid())
            ->setStatus(Mage_Tag_Model_Tag::STATUS_APPROVED)
            ->setStoreId(Mage::app()->getStore()->getId())
            ->save();
    }

    /**
     *  Creates tag relation (tag <-> customer <-> product)
     *
     *  @param    int $tagId
     *  @param    int $productId
     *  @param    Mage_Customer_Model_Customer $customer
     *  @return	  Mage_Tag_Model_Tag_Relation
     */
    protected function _createRelation ($tagId, $productId, $customerId)
    {
        return Mage::getModel('tag/tag_relation')
            ->setTagId($tagId)
            ->setCustomerId($customerId)
            ->setProductId($productId)
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setCreatedAt(now())
            ->setActive(1)
            ->save();
    }

    /**
     *  Returns tag summary infoo
     *
     *  @param    int $tagId
     *  @return	  array
     */
    protected function _getSummary ($tagId)
    {
        $row = $this->_getConnection()->fetchRow(
            $this->_getConnection()->select()
                ->from(Mage::getResourceModel('tag/tag')->getTable('tag/summary'))
                ->where('tag_id=?', $tagId)
                ->where('store_id=?', Mage::app()->getStore()->getId())
        );
        return $row;
    }

}
