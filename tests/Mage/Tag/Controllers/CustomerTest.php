<?php
if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../../PHPUnitTestInit.php';
    $suite = new PHPUnit_Framework_TestSuite();
    $suite->addTestSuite('Mage_Tag_Controllers_ProductTest');
    PHPUnit_TextUI_TestRunner::run($suite);
    exit;
}

class Mage_Tag_Controllers_CustomerTest extends PHPUnit_Framework_TestCase
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

        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($this->_customer);
    }

    /**
     * Check if added product tag is at the customer tags list page
     *
     */
//    public function testIndexAction()
//    {
//        ob_start();
//        try {
//            Mage::app()->getFrontController()->getRequest()
//                ->setModuleName('tag')
//                ->setControllerName('customer')
//                ->setActionName('index');
//            Mage::app()->getFrontController()->dispatch();
//
//            $this->assertThat(
//                Mage::getSingleton('core/layout')->getBlock('customer_tags'),
//                new PHPUnit_Framework_Constraint_IsInstanceOf(Mage::getConfig()->getBlockClassName('tag/customer_tags'))
//            );
//            $contents = ob_get_clean();
//            $this->assertContains($this->_tag->getName(), $contents);
//        }
//        catch (Exception $e) {
//            ob_get_clean();
//            throw $e;
//        }
//    }
//
//    public function testViewAction()
//    {
//        ob_start();
//        try {
//            Mage::app()->getFrontController()->getRequest()
//                ->setModuleName('tag')
//                ->setControllerName('customer')
//                ->setActionName('view')
//                ->setParam('tagId', $this->_tag->getId());
//            Mage::app()->getFrontController()->dispatch();
//
//            $this->assertThat(
//                Mage::getSingleton('core/layout')->getBlock('customer_view'),
//                new PHPUnit_Framework_Constraint_IsInstanceOf(Mage::getConfig()->getBlockClassName('tag/customer_view'))
//            );
//            $contents = ob_get_clean();
//            file_put_contents('tag.html', $contents);
//            $this->assertContains($this->_product->getName(), $contents);
//        }
//        catch (Exception $e) {
//            ob_get_clean();
//            throw $e;
//        }
//    }

//    public function testEditAction()
//    {
//        ob_start();
//        try {
//            Mage::app()->getFrontController()->getRequest()
//                ->setModuleName('tag')
//                ->setControllerName('customer')
//                ->setActionName('edit')
//                ->setParam('tagId', $this->_tag->getId());
//            Mage::app()->getFrontController()->dispatch();
//
//            $this->assertThat(
//                Mage::getSingleton('core/layout')->getBlock('customer_edit'),
//                new PHPUnit_Framework_Constraint_IsInstanceOf(Mage::getConfig()->getBlockClassName('tag/customer_edit'))
//            );
//            $contents = ob_get_clean();
//            $this->assertContains($this->_tag->getName(), $contents);
//        }
//        catch (Exception $e) {
//            ob_get_clean();
//            throw $e;
//        }
//    }

    public function testRemoveAction()
    {
        ob_start();
        try {
            $_oldTagId = $this->_tag->getId();
            Mage::app()->getFrontController()->getRequest()
                ->setModuleName('tag')
                ->setControllerName('customer')
                ->setActionName('remove')
                ->setParam('tagId', $this->_tag->getId());
            Mage::app()->getFrontController()->dispatch();

            $contents = ob_get_clean();
            $this->_tag->addSummary(Mage::app()->getStore()->getId());
            $this->assertNull($this->_tag->getCustomers());
        }
        catch (Exception $e) {
            ob_get_clean();
            throw $e;
        }
    }

//    public function testSaveAction()
//    {
//        ob_start();
//        try {
//            $newTagName = 'new_'.$this->_tag->getName();
//            $_POST['tagName'] = $newTagName;
//            Mage::app()->getFrontController()->getRequest()
//                ->setModuleName('tag')
//                ->setControllerName('customer')
//                ->setActionName('save')
//                ->setParam('tagId', $this->_tag->getId());
//            Mage::app()->getFrontController()->dispatch();
//
//            $contents = ob_get_clean();
//
//            $this->_tag = Mage::getModel('tag/tag')->loadByName($newTagName);
//            $this->assertGreaterThan(0, $this->_tag->getId());
//        }
//        catch (Exception $e) {
//            ob_get_clean();
//            throw $e;
//        }
//    }

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
            Mage::getSingleton('customer/session')->logout();
            $this->_customer->delete();
        }
    }
}
