<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once (realpath(dirname(__FILE__) . '/../../../../../../../')
    . '/app/code/Mage/Adminhtml/controllers/CacheController.php');

class Mage_Adminhtml_CacheControllerTest extends PHPUnit_Framework_TestCase
{
    public function testCleanMediaAction()
    {
        // Wire object with mocks
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $response = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false);
        $objectManager = $this->getMock('Magento_ObjectManager');
        $frontController = $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false);
        $layoutFactory = $this->getMock('Mage_Core_Model_Layout_Factory', array(), array($objectManager));
        $cache = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false);
        $cacheTypes = $this->getMock('Mage_Core_Model_Cache_Types', array(), array(), '', false);
        $cacheFrontendPool = $this->getMock('Mage_Core_Model_Cache_Frontend_Pool', array(), array(), '', false);

        $backendHelper = $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false);
        $invokeArgs = array(
            'translator' => $this->getMock('Mage_Core_Model_Translate', array(), array(), '', false),
            'session' => $this->getMock('Mage_Backend_Model_Session', array(), array(), '', false),
            'helper' => $backendHelper,
        );
        $controller = new Mage_Adminhtml_CacheController(
            $request,
            $response,
            $objectManager,
            $frontController,
            $layoutFactory,
            $cache,
            $cacheTypes,
            $cacheFrontendPool,
            null,
            $invokeArgs
        );

        // Setup expectations
        $mergeService = $this->getMock('Mage_Core_Model_Page_Asset_MergeService', array(), array(), '', false);
        $mergeService->expects($this->once())
            ->method('cleanMergedJsCss');

        $eventManager = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false);
        $eventManager->expects($this->once())
            ->method('dispatch')
            ->with('clean_media_cache_after');

        $helper = $this->getMock('Mage_Adminhtml_Helper_Data', array(), array(), '', false);
        $helper->expects($this->once())
            ->method('__')
            ->with('The JavaScript/CSS cache has been cleaned.')
            ->will($this->returnValue('Translated value'));

        $session = $this->getMock('Mage_Adminhtml_Model_Session', array(), array(), '', false);
        $session->expects($this->once())
            ->method('addSuccess')
            ->with('Translated value');

        $valueMap = array(
            array('Mage_Core_Model_Page_Asset_MergeService', $mergeService),
            array('Mage_Core_Model_Event_Manager', $eventManager),
            array('Mage_Adminhtml_Helper_Data', $helper),
            array('Mage_Adminhtml_Model_Session', $session),
        );
        $objectManager->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($valueMap));

        $backendHelper->expects($this->once())
            ->method('getUrl')
            ->with('*/*')
            ->will($this->returnValue('redirect_url'));
        $response->expects($this->once())
            ->method('setRedirect')
            ->with('redirect_url');

        // Run
        $controller->cleanMediaAction();
    }
}
