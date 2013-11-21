<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Controller;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    public function testCleanMediaAction()
    {
        // Wire object with mocks
        $response = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $objectManager = $this->getMock('Magento\ObjectManager');
        $eventManager = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $backendHelper = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $session = $this->getMock('Magento\Adminhtml\Model\Session', array('addSuccess'), array(), '', false);
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $controller = $helper->getObject('Magento\Backend\Controller\Adminhtml\Cache', array(
                'objectManager' => $objectManager,
                'response' => $response,
                'helper' => $backendHelper,
                'eventManager' => $eventManager
            )
        );

        // Setup expectations
        $mergeService = $this->getMock('Magento\Core\Model\Page\Asset\MergeService', array(), array(), '', false);
        $mergeService->expects($this->once())
            ->method('cleanMergedJsCss');

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
        // Run
        $controller->cleanMediaAction();
    }
}
