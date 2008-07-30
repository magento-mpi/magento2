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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract for Tag controllers tests
 *
 */
abstract class Mage_Tag_Controllers_AbstractTestCase extends PHPUnit_Framework_TestCase
{
    protected $_product;
    protected $_tag;
    protected $_tagRelation;
    protected $_customer;

    /**
     * Add a product, customer, tag and its relations
     *
     */
    protected function setUp()
    {
        $storeId = Mage::app()->getStore()->getId();
        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $defaultAttributeSetId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getDefaultAttributeSetId();

        $this->_product = Mage::getModel('catalog/product')
            ->setTypeId('simple')
            ->setStoreId($storeId)
            ->setName(uniqid())
            ->setDescription('test desc')
            ->setShortDescription('test shortdesc')
            ->setSku(uniqid())
            ->setWeight(1)
            ->setStatus(1)
            ->setVisibility(4)
            ->setPrice(100)
            ->setWebsiteIds(array($websiteId))
            ->setAttributeSetId($defaultAttributeSetId)
            ->save();
        $this->_customer = Mage::getModel('customer/customer')
            ->setStoreId($storeId)
            ->setFirstname(uniqid())
            ->setLastname(uniqid())
            ->setEmail(uniqid() . '@varien.com')
            ->setGroupId('general')
            ->setPassword(uniqid())
            ->setCreatedIn($websiteId)
            ->setIsSubscribed(false)
            ->setConfirmation(null)
            ->save();
        $this->_tag = Mage::getModel('tag/tag')
            ->setName(uniqid())
            ->setStatus(1)
            ->setStoreId($storeId)
            ->save();
        $this->_tagRelation = Mage::getModel('tag/tag_relation')
            ->setTagId($this->_tag->getId())
            ->setCustomerId($this->_customer->getId())
            ->setStoreId($this->_customer->getStoreId())
            ->setActive(1)
            ->setProductId($this->_product->getId())
            ->save();
        $this->_tag->aggregate();
    }

    /**
     * Delete test temporary data
     *
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
        Mage::reset();
    }
}
