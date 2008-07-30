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


/**
 * DESCRIPTION
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
                Mage::getModel('tag/tag_relation')
                    ->setTagId($tag->getId())
                    ->setCustomerId($customer->getId())
                    ->setStoreId($customer->getStoreId())
                    ->setActive(Mage_Tag_Model_Tag_Relation::STATUS_ACTIVE)
                    ->setProductId($product->getId())
                    ->save();
            }
        }

        $tag->aggregate();

        $tagResource = Mage::getResourceModel('tag/tag');
        /* @var $tagResource Mage_Tag_Model_Mysql4_Tag */

        $row = $this->_getConnection()->fetchRow(
            $this->_getConnection()->select()
                ->from($tagResource->getTable('tag/summary'))
                ->where('tag_id=?', $tag->getId())
                ->where('store_id=?', Mage::app()->getStore()->getId())
        );

        $this->assertEquals($row, array(
            'tag_id'            => $tag->getId(),
            'store_id'          => Mage::app()->getStore()->getId(),
            'customers'         => $customerCount,
            'products'          => $productCount,
            'uses'              => $productCount * $customerCount,
            'historical_uses'   => $productCount * $customerCount,
            'popularity'        => $productCount * $customerCount
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
}
