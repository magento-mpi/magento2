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
 * @package    Mage_PackageName
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../../PHPUnitTestInit.php';
    PHPUnitTestInit::runMe(__FILE__);
}

/**
 * Tag_Relation test case
 *
 * @name       Mage_Tag_Model_TagTest
 * @author	   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_TagRelationTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        Mage::app();
    }

    /**
     * tests the loading relation by customer and tag
     */
    public function testLoadByTagCustomer ()
    {
        $this->_createRelation();

        $this->_tagRelation->loadByTagCustomer(
            $this->_product->getId(),
            $this->_tag->getId(),
            $this->_customer->getId(),
            Mage::app()->getStore()->getId()
        );

        $relation = $this->_tagRelation->getData();
        $relation = array(
            'tag_relation_id'   => $relation['tag_relation_id'],
            'tag_id'            => $relation['tag_id'],
            'customer_id'       => $relation['customer_id'],
            'product_id'        => $relation['product_id'],
            'store_id'          => $relation['store_id'],
            'name'              => $relation['name']
        );

        $this->assertEquals($relation, array(
            'tag_relation_id'   => $this->_tagRelation->getId(),
            'tag_id'            => $this->_tag->getId(),
            'customer_id'       => $this->_customer->getId(),
            'product_id'        => $this->_product->getId(),
            'store_id'          => Mage::app()->getStore()->getId(),
            'name'              => $this->_tag->getName(),
        ));
    }

    /**
     * tests the retrieving tag related product ids
     */
    public function testGetProductIds ()
    {
        $this->_createRelation();
        $this->_tagRelation->setTagId($this->_tag->getId());
        $this->_tagRelation->setCustomerId($this->_customer->getId());
        $ids = $this->_tagRelation->getProductIds();
        $this->assertEquals($ids, array(0 => $this->_product->getId()));
    }

    /**
     *  tests tag relation deactivation
     */
    public function testDeactivate ()
    {
        $this->_createRelation();
        $this->_tagRelation->setTagId($this->_tag->getId());
        $this->_tagRelation->setCustomerId($this->_customer->getId());
        $this->_tagRelation->deactivate();
        $this->_tagRelation->loadByTagCustomer(
            $this->_product->getId(),
            $this->_tag->getId(),
            $this->_customer->getId(),
            Mage::app()->getStore()->getId()
        );
        $this->assertTrue(!$this->_tagRelation->getActive());
    }


    /**
     * Delete test temporary data
     */
    protected function tearDown()
    {
        if ($this->_product) {
            $this->_product->delete();
        }
        if ($this->_tag) {
            $this->_tag->delete();
        }
        if ($this->_tagRelation) {
            $this->_tagRelation->delete();
        }
        if ($this->_customer) {
            $this->_customer->delete();
        }
    }

    /**
     *  Creates relation in DB; product, tag, customer init
     */
    public function _createRelation ()
    {
        $this->_product = $this->_createProduct();
        $this->_tag = $this->_createTag();
        $this->_customer = $this->_createCustomer();

        $this->_tagRelation = Mage::getModel('tag/tag_relation');
        $this->_tagRelation->setTagId($this->_tag->getId())
            ->setCustomerId($this->_customer->getId())
            ->setProductId($this->_product->getId())
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setCreatedAt(now())
            ->setActive(1)
            ->save();
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