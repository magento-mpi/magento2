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
        $response = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $request = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);

        $objectManager = $this->getMock('Magento\ObjectManager');
        $backendHelper = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $session = $this->getMock(
            'Magento\Backend\Model\Session',
            array('setIsUrlNotice'),
            $helper->getConstructArguments('Magento\Backend\Model\Session')
        );
        $messageManager = $this->getMock(
            'Magento\Message\Manager',
            array('addSuccess'),
            $helper->getConstructArguments('Magento\Message\Manager')
        );
        $context = $this->getMock(
            'Magento\Backend\App\Action\Context',
            array('getRequest', 'getResponse', 'getMessageManager', 'getSession'),
            $helper->getConstructArguments('Magento\Backend\App\Action\Context', array(
                'session' => $session,
                'response' => $response,
                'objectManager' => $objectManager,
                'helper' => $backendHelper,
                'request' => $request,
                'messageManager' => $messageManager
            ))
        );
        $context->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $context->expects($this->once())->method('getResponse')->will($this->returnValue($response));
        $context->expects($this->once())->method('getSession')->will($this->returnValue($session));
        $context->expects($this->once())->method('getMessageManager')->will($this->returnValue($messageManager));
        $controller = $helper->getObject('Magento\Backend\Controller\Adminhtml\Cache', array(
            'context' => $context
        ));

        // Setup expectations
        $mergeService = $this->getMock('Magento\View\Asset\MergeService', array(), array(), '', false);
        $mergeService->expects($this->once())
            ->method('cleanMergedJsCss');

        $messageManager->expects($this->once())
            ->method('addSuccess')
            ->with('The JavaScript/CSS cache has been cleaned.');

        $session->expects($this->once())
            ->method('setIsUrlNotice')
            ->will($this->returnSelf());

        $valueMap = array(
            array('Magento\View\Asset\MergeService', $mergeService),
            array('Magento\Session\SessionManager', $session),
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
        // Run
        $controller->cleanMediaAction();
    }
}
