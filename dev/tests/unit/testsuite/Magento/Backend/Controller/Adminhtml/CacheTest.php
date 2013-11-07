<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Controller\Adminhtml;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    public function testCleanMediaAction()
    {
        // Wire object with mocks
        $context = $this->getMock('Magento\Backend\Controller\Context', array(), array(), '', false);

        $request = $this->getMock('Magento\App\RequestInterface', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $response = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($response));

        $objectManager = $this->getMock('Magento\ObjectManager');
        $context->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManager));

        $frontController = $this->getMock('Magento\App\FrontController', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getFrontController')
            ->will($this->returnValue($frontController));

        $eventManager = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $eventManager->expects($this->once())
            ->method('dispatch')
            ->with('clean_media_cache_after');
        $context->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));

        $backendHelper = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getHelper')
            ->will($this->returnValue($backendHelper));

        $cacheTypeListMock = $this->getMock('Magento\Core\Model\Cache\TypeListInterface');
        $cacheStateMock = $this->getMock('Magento\Core\Model\Cache\StateInterface');
        $cacheFrontendPool = $this->getMock('Magento\Core\Model\Cache\Frontend\Pool', array(), array(), '', false);

        $controller = new \Magento\Backend\Controller\Adminhtml\Cache(
            $context,
            $cacheTypeListMock,
            $cacheStateMock,
            $cacheFrontendPool
        );

        // Setup expectations
        $mergeService = $this->getMock('Magento\Core\Model\Page\Asset\MergeService', array(), array(), '', false);
        $mergeService->expects($this->once())
            ->method('cleanMergedJsCss');

        $session = $this->getMock('Magento\Adminhtml\Model\Session', array(), array(), '', false);
        $session->expects($this->once())
            ->method('addSuccess')
            ->with('The JavaScript/CSS cache has been cleaned.');

        $valueMap = array(
            array('Magento\Core\Model\Page\Asset\MergeService', $mergeService),
            array('Magento\Adminhtml\Model\Session', $session),
        );
        $objectManager->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($valueMap));

        $backendHelper->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/*')
            ->will($this->returnValue('redirect_url'));

        $response->expects($this->once())
            ->method('setRedirect')
            ->with('redirect_url');

        $response->expects($this->once())
            ->method('getHeader')
            ->with('X-Frame-Options')
            ->will($this->returnValue(false));

        // Run
        $controller->cleanMediaAction();
    }
}
