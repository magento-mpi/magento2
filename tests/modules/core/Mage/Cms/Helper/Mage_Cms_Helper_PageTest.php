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
//        $this->_getDbAdapter()->loadFixture('cms');
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
     * testing unable to load :)
     *
     * @dataProvider dataNoPageLoad
     *
     */
    public function testRenderPageNoPageLoad ($pageId, $returnLoad)
    {
        $action = $this->getMock('Mage_Core_Controller_Front_Action', array('renderLayout'), array(
            Mage::app()->getFrontController()->getRequest(),
            Mage::app()->getFrontController()->getResponse(),
            array()
        ));

        Mage::unregister('_singleton/cms/page');
        $mock = $this->getModelMock('cms/page', array('getId', 'load'));
        $mock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($pageId + 1));

        $mock->expects($this->once())
            ->method('load')
            ->will($this->returnValue($returnLoad));

        $resultOfRun = Mage::helper('cms/page')->renderPage($action, $pageId);
        $this->assertFalse($resultOfRun, 'unable to $page->load if pageId is false');
    }

    public function dataNoPageLoad()
    {
        return array(
            array(1, false),
            array(0, false)
        );
    }

/*
 * TODO need to test $page->getCustomTheme();
 */

    /**
     * Tests Mage_Cms_Helper_Page->renderPage()
     * No theme
     *
     */
    public function testRenderPageNoCustomTheme ()
    {
        $pageId = 1;
        $action = $this->getMock('Mage_Core_Controller_Front_Action', array('renderLayout'), array(
            Mage::app()->getFrontController()->getRequest(),
            Mage::app()->getFrontController()->getResponse(),
            array()
        ));

        Mage::unregister('_singleton/cms/page');
        $this->getModelMock('cms/page', array('getRootTemplate'));
        $mock = Mage::getSingleton('cms/page')->setData(array(
            'page_id'       => $pageId,
            'custom_theme'  => 'default/modern'
        ));

        $mock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($pageId));

        $mock->expects($this->exactly(2))
            ->method('getRootTemplate')
            ->will($this->returnValue(false));

        $layout = $this->getHelperMock('page/layout', array('applyHandle', 'applyLayout'));

        $layout->expects($this->exactly(0))
            ->method('applyHandle');
        $layout->expects($this->exactly(0))
            ->method('applyLayout');
        $action->expects($this->once())
            ->method('renderLayout');
/*
Если зайти в ту ветку, где проверяется рут темплейт, то в результате не должен
выполнится второй раз этот метод (при передаче $action->getLayout()
->helper('page/layout')->applyHandle($page->getRootTemplate());), ЧТД
*/
        // code to run helper

//        mage::app()->getFrontController()->
        Mage::helper('cms/page')->renderPage($action, $pageId);
    }

    public function testRenderPageNoGetSessionMessages()
    {
        $pageId = 1;
        Mage::getSingleton('cms/page')->setData(array(
            'page_id'       => $pageId,
        ));

        $action = new Mage_Core_Controller_Front_Action(
            Mage::app()->getFrontController()->getRequest(),
            Mage::app()->getFrontController()->getResponse(),
            array()
        );

        Mage::unregister('_singleton/catalog/session');
        $sessionMock = $this->getModelMock('catalog/session', array('getMessages'));

        $sessionMock->expects($this->once())
            ->method('getMessages');

        Mage::helper('cms/page')->renderPage($action, $pageId);
    }
}
