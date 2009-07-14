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
 * @category    Tests
 * @package     Mage_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Cms Index Controller Test Case
 *
 * @category    Tests
 * @package     Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_IndexControllerTest extends Mage_TestCase
{
    /**
     * Prepares the environment before running a test.
     *
     */
    protected function setUp ()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     *
     */
    protected function tearDown ()
    {
        parent::tearDown();
    }

    public function testForTrue()
    {
        $this->assertTrue(TRUE);
    }
    /**
     * Tests Mage_Cms_IndexController->defaultIndexAction()
     *
     * @param string $action
     */
    public function testDefaultIndexAction($action = 'cms/index/defaultIndex')
    {
        $response = $this->_runControllerAction($action);
        $this->assertEquals($this->_getHeaderByName($response->getHeaders(), 'Status'),
            '404 File not found');
    }
    /**
     * Tests Mage_Cms_IndexController->defaultNoCookiesAction()
     */
    public function testDefaultNoCookiesAction()
    {
        // TODO Auto-generated Mage_Cms_IndexControllerTest->testDefaultNoCookiesAction()
        $this->markTestIncomplete("defaultNoCookiesAction test not implemented");
        $this->Mage_Cms_IndexController->defaultNoCookiesAction(/* parameters */);
    }
    /**
     * Tests Mage_Cms_IndexController->defaultNoRouteAction()
     */
    public function testDefaultNoRouteAction()
    {
        // TODO Auto-generated Mage_Cms_IndexControllerTest->testDefaultNoRouteAction()
        $this->markTestIncomplete("defaultNoRouteAction test not implemented");
        $this->Mage_Cms_IndexController->defaultNoRouteAction(/* parameters */);
    }
    /**
     * Tests Mage_Cms_IndexController->indexAction()
     */
    public function testIndexAction()
    {
        $collection = Mage::getModel('cms/page')
            ->getCollection()
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToFilter('is_active', 1)
            ->setPageSize(1)
            ->setCurPage(2);
        /* @var $page Mage_Cms_Model_Page */
        $page = $collection->getFirstItem();

        // reset cms page helper singleton
        Mage::getSingleton('cms/page')
            ->setData(array());
        // modify config on first exists page
        Mage::app()->getStore()->setConfig(
            Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE,
            $page->getId());

        $response = $this->_runControllerAction('cms/index/index');

        $this->assertContains($page->getTitle(), $response->getBody(), 'We don\'t have title :(');

        // reset cms page helper singleton
        Mage::getSingleton('cms/page')->setData(array());
        // modify config to not exists page
        Mage::app()->getStore()->setConfig(
            Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE, -1);

        // check forward to default page
        $this->testDefaultIndexAction('cms/index/index');
    }
    /**
     * Tests Mage_Cms_IndexController->noCookiesAction()
     */
    public function testNoCookiesAction ()
    {
        // TODO Auto-generated Mage_Cms_IndexControllerTest->testNoCookiesAction()
        $this->markTestIncomplete("noCookiesAction test not implemented");
        $this->Mage_Cms_IndexController->noCookiesAction(/* parameters */);
    }
    /**
     * Tests Mage_Cms_IndexController->noRouteAction()
     *
     */
    public function testNoRouteAction ()
    {
        // TODO Auto-generated Mage_Cms_IndexControllerTest->testNoRouteAction()
        $this->markTestIncomplete("noRouteAction test not implemented");
        $this->Mage_Cms_IndexController->noRouteAction(/* parameters */);
    }
}

