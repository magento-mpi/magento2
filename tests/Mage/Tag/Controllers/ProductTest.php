<?php
if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../../PHPUnitTestInit.php';
    $suite = new PHPUnit_Framework_TestSuite();
    $suite->addTestSuite('Mage_Tag_Controllers_ProductTest');
    PHPUnit_TextUI_TestRunner::run($suite);
    exit;
}

class Mage_Tag_Controllers_ProductTest extends PHPUnit_Framework_TestCase
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
     * Check if added product tag is at the page
     *
     */
    public function testListAction()
    {
        ob_start();
        try {
            Mage::app()->getFrontController()->getRequest()
                ->setModuleName('tag')
                ->setControllerName('product')
                ->setActionName('list')
                ->setParam('tagId', $this->_tag->getId());
            Mage::app()->getFrontController()->dispatch();

            $this->assertThat(
                Mage::getSingleton('core/layout')->getBlock('tag_products'),
                new PHPUnit_Framework_Constraint_IsInstanceOf(Mage::getConfig()->getBlockClassName('tag/product_result'))
            );
            $contents = ob_get_clean();
            $this->assertContains($this->_tag->getName(), $contents);
            $this->assertContains($this->_product->getName(), $contents);
        }
        catch (Exception $e) {
            ob_get_clean();
            throw $e;
        }
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
    }
}
