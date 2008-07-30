<?php
if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../../PHPUnitTestInit.php';
    $suite = new PHPUnit_Framework_TestSuite();
    $suite->addTestSuite('Mage_Tag_Controllers_IndexTest');
    PHPUnit_TextUI_TestRunner::run($suite);
    exit;
}

class Mage_Tag_Controllers_IndexTest extends PHPUnit_Framework_TestCase
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
     * Check if tag is added by saveAction
     *
     */
    public function testSaveAction()
    {
        ob_start();
        try {
            // create unique tag name
            for ($i = 0; ; $i++) {
                $tagName = uniqid();
                $tag = Mage::getModel('tag/tag')
                    ->loadByName($tagName);
                if (0 == $tag->getId()) {
                    break;
                }
                if ($i > 10) {
                    $this->fail('Failed to generate unique random tag.');
                }
            }
            // create a success url
            $successUrl = uniqid();
            // log in customer
            $session = Mage::getSingleton('customer/session')
                ->setCustomerAsLoggedIn($this->_customer);

            // dispatch controller
            $_GET['tagName'] = $tagName;
            Mage::app()->getFrontController()->getRequest()
                ->setModuleName('tag')
                ->setControllerName('index')
                ->setActionName('save')
                ->setParam('product', $this->_product->getId())
                ->setParam(Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED, $successUrl)
            ;
            Mage::app()->getFrontController()->dispatch();

            // check if our new tag has been saved
            $tag = Mage::getModel('tag/tag')
                ->loadByName($tagName);
            $this->assertTrue(0 != $tag->getId(), sprintf('Failed to save tag "%s".', $tagName));

            // check if our new tag has pending status
            $this->assertTrue($tag->getPendingStatus() == $tag->getStatus(), 'New tag has wrong status. Expected pending.');

            // check if our new tag is related to product/customer/store
            $relation = Mage::getModel('tag/tag_relation')->loadByTagCustomer(
                $this->_product->getId(), $tag->getId(), $this->_customer->getId(), $this->_customer->getStoreId()
            );
            $this->assertTrue(0 != $relation->getId(), 'Tag saved, but relation is not.');
            $this->assertTrue(
                (0 != $relation->getTagId()) && (0 != $relation->getCustomerId()) && (0 != $relation->getProductId()),
                'Tag saved, but relation is corrupt. Expected aggregated relation.'
            );

            // dispose of tag
            $tag->delete();
            // logoff customer
            $session->logout();

            $contents = ob_get_clean();
        }
        catch (Exception $e) {
            if ($tag && $tag->getId()) {
                $tag->delete();
            }
            if ($session && $session->isLoggedIn()) {
                $session->logout();
            }
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
