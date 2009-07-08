<?php
/**
 * Mage_Cms_Helper_Page test case.
 */
class Mage_Cms_Helper_PageTest extends Mage_TestCase
{
    /**
     * Helper instance
     *
     * @var Mage_Cms_Helper_Page
     */
    protected $_helper;

    /**
     * Prepares the environment before running a test
     * Load fixtures
     *
     */
    protected function setUp ()
    {
        parent::setUp();
        $this->_getDbAdapter()->loadFixture('cms');
        $this->_helper = new Mage_Cms_Helper_Page();
    }

    /**
     * Cleans up the environment after running a test.
     *
     */
    protected function tearDown ()
    {
        parent::tearDown();
        Mage::getSingleton('cms/page')
            ->setData(array())
            ->setOrigData();
    }

    /**
     * Tests Mage_Cms_Helper_Page->getPageUrl()
     *
     */
    public function testGetPageUrl ()
    {
        $pageFixtureRow = $this->_getDbAdapter()->getTableRow('cms_page', 1);

        $helperUrl = $this->_helper->getPageUrl(-1);
        $this->assertNull($helperUrl);

        $url = Mage::getUrl(null, array('_direct' => $pageFixtureRow->getIdentifier()));
        $helperUrl = $this->_helper->getPageUrl($pageFixtureRow->getPageId());

        $this->assertEquals($url, $helperUrl);
    }

    /**
     * Tests Mage_Cms_Helper_Page->renderPage()
     *
     */
    public function testRenderPage ()
    {
        // TODO Auto-generated Mage_Cms_Helper_PageTest->testRenderPage()
        $this->markTestIncomplete("renderPage test not implemented");
        $this->Mage_Cms_Helper_Page->renderPage(/* parameters */);
    }
}
