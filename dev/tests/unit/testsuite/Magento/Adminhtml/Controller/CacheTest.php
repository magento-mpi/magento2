<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Controller_CacheTest extends PHPUnit_Framework_TestCase
{
    public function testCleanMediaAction()
    {
        // Wire object with mocks
        $context = $this->getMock('Magento_Backend_Controller_Context', array(), array(), '', false);

        $request = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $response = $this->getMock('Magento_Core_Controller_Response_Http', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($response));

        $objectManager = $this->getMock('Magento\ObjectManager');
        $context->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager));

        $frontController = $this->getMock('Magento_Core_Controller_Varien_Front', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getFrontController')
            ->will($this->returnValue($frontController));

        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $eventManager->expects($this->once())
            ->method('dispatch')
            ->with('clean_media_cache_after');
        $context->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));

        $backendHelper = $this->getMock('Magento_Backend_Helper_Data', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getHelper')
            ->will($this->returnValue($backendHelper));

        $cacheTypeListMock = $this->getMock('Magento_Core_Model_Cache_TypeListInterface');
        $cacheStateMock = $this->getMock('Magento_Core_Model_Cache_StateInterface');
        $cacheFrontendPool = $this->getMock('Magento_Core_Model_Cache_Frontend_Pool', array(), array(), '', false);

        $controller = new Magento_Adminhtml_Controller_Cache(
            $context,
            $cacheTypeListMock,
            $cacheStateMock,
            $cacheFrontendPool
        );

        // Setup expectations
        $mergeService = $this->getMock('Magento_Core_Model_Page_Asset_MergeService', array(), array(), '', false);
        $mergeService->expects($this->once())
            ->method('cleanMergedJsCss');

        $session = $this->getMock('Magento_Adminhtml_Model_Session', array(), array(), '', false);
        $session->expects($this->once())
            ->method('addSuccess')
            ->with('The JavaScript/CSS cache has been cleaned.');

        $valueMap = array(
            array('Magento_Core_Model_Page_Asset_MergeService', $mergeService),
            array('Magento_Adminhtml_Model_Session', $session),
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
