<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../../../../../../app/code/Mage/Adminhtml/controllers/CacheController.php';

class Mage_Adminhtml_CacheControllerTest extends PHPUnit_Framework_TestCase
{
    public function testCleanMediaAction()
    {
        // Wire object with mocks
        $context = $this->getMock('Mage_Backend_Controller_Context', array(), array(), '', false);

        $request = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $response = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($response));

        $objectManager = $this->getMock('Magento_ObjectManager');
        $context->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager));

        $frontController = $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getFrontController')
            ->will($this->returnValue($frontController));

        $eventManager = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false);
        $eventManager->expects($this->once())
            ->method('dispatch')
            ->with('clean_media_cache_after');
        $context->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));

        $backendHelper = $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getHelper')
            ->will($this->returnValue($backendHelper));

        $cache = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false);
        $cacheTypes = $this->getMock('Mage_Core_Model_Cache_Types', array(), array(), '', false);
        $cacheFrontendPool = $this->getMock('Mage_Core_Model_Cache_Frontend_Pool', array(), array(), '', false);

        $controller = new Mage_Adminhtml_CacheController(
            $context,
            $cache,
            $cacheTypes,
            $cacheFrontendPool,
            null
        );

        // Setup expectations
        $mergeService = $this->getMock('Mage_Core_Model_Page_Asset_MergeService', array(), array(), '', false);
        $mergeService->expects($this->once())
            ->method('cleanMergedJsCss');

        $session = $this->getMock('Mage_Adminhtml_Model_Session', array(), array(), '', false);
        $session->expects($this->once())
            ->method('addSuccess')
            ->with('The JavaScript/CSS cache has been cleaned.');

        $valueMap = array(
            array('Mage_Core_Model_Page_Asset_MergeService', $mergeService),
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
