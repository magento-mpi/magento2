<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Controller;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\OptionProvider;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\UrlRewrite\Controller\Router */
    protected $router;

    /** @var \Magento\Framework\App\ActionFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $actionFactory;

    /** @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $url;

    /** @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject */
    protected $state;

    /** @var \Magento\Framework\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject */
    protected $store;

    /** @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $response;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var \Magento\UrlRewrite\Model\UrlFinderInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlFinder;

    protected function setUp()
    {
        $this->actionFactory = $this->getMock('Magento\Framework\App\ActionFactory', [], [], '', false);
        $this->url = $this->getMock('Magento\Framework\UrlInterface');
        $this->state = $this->getMock('Magento\Framework\App\State', [], [], '', false);
        $this->storeManager = $this->getMock('Magento\Framework\StoreManagerInterface');
        $this->response = $this->getMock('Magento\Framework\App\ResponseInterface', ['setRedirect', 'sendResponse']);
        $this->request = $this->getMockBuilder('\Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()->getMock();
        $this->urlFinder = $this->getMock('Magento\UrlRewrite\Model\UrlFinderInterface');
        $this->store = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();


        $this->router = (new ObjectManager($this))->getObject(
            'Magento\UrlRewrite\Controller\Router',
            [
                'actionFactory' => $this->actionFactory,
                'url' => $this->url,
                'appState' => $this->state,
                'storeManager' => $this->storeManager,
                'response' => $this->response,
                'urlFinder' => $this->urlFinder
            ]
        );
    }

    public function testAppNotInstalled()
    {

        $this->state->expects($this->once())->method('isInstalled')->will($this->returnValue(false));
        $this->response->expects($this->once())->method('setRedirect')->with('install-url')->will($this->returnSelf());
        $this->response->expects($this->once())->method('sendResponse');
        $this->url->expects($this->once())->method('getUrl')->with('install')->will($this->returnValue('install-url'));

        $this->assertEquals(null, $this->router->match($this->request));
    }

    public function testNoRewriteExist()
    {
        $this->state->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->urlFinder->expects($this->any())->method('findOneByData')->will($this->returnValue(null));
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->store));
        $this->store->expects($this->any())->method('getId')->will($this->returnValue('current-store-id'));

        $this->assertEquals(null, $this->router->match($this->request));
    }

    public function testRewriteAfterStoreSwitcher()
    {
        $this->state->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->request->expects($this->any())->method('getPathInfo')->will($this->returnValue('request-path'));
        $this->request->expects($this->any())->method('getParam')->with('___from_store')
            ->will($this->returnValue('old-store'));
        $oldStore = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $this->storeManager->expects($this->any())->method('getStore')
            ->will($this->returnValueMap([['old-store', $oldStore], [null, $this->store]]));
        $oldStore->expects($this->any())->method('getId')->will($this->returnValue('old-store-id'));
        $this->store->expects($this->any())->method('getId')->will($this->returnValue('current-store-id'));
        $oldUrlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $oldUrlRewrite->expects($this->any())->method('getEntityType')->will($this->returnValue('entity-type'));
        $oldUrlRewrite->expects($this->any())->method('getEntityId')->will($this->returnValue('entity-id'));
        $oldUrlRewrite->expects($this->any())->method('getRequestPath')->will($this->returnValue('old-request-path'));
        $urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $urlRewrite->expects($this->any())->method('getRequestPath')->will($this->returnValue('new-request-path'));

        $this->urlFinder->expects($this->any())->method('findOneByData')->will(
            $this->returnValueMap(
                [
                    [
                        [UrlRewrite::REQUEST_PATH => 'request-path', UrlRewrite::STORE_ID => 'old-store-id'],
                        $oldUrlRewrite
                    ],
                    [
                        [
                            UrlRewrite::ENTITY_TYPE => 'entity-type',
                            UrlRewrite::ENTITY_ID => 'entity-id',
                            UrlRewrite::STORE_ID => 'current-store-id',
                            UrlRewrite::IS_AUTOGENERATED => 1,
                        ],
                        $urlRewrite
                    ]
                ]
            )
        );
        $this->response->expects($this->once())->method('setRedirect')
            ->with('request-path-302', OptionProvider::TEMPORARY);
        $this->url->expects($this->once())->method('getUrl')->with('', array('_direct' => 'new-request-path'))
            ->will($this->returnValue('request-path-302'));
        $this->request->expects($this->once())->method('setDispatched')->with(true);
        $this->actionFactory->expects($this->once())->method('create')
            ->with('Magento\Framework\App\Action\Redirect', ['request' => $this->request]);

        $this->router->match($this->request);
    }

    public function testNoRewriteAfterStoreSwitcher()
    {
        $this->state->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->request->expects($this->any())->method('getPathInfo')->will($this->returnValue('request-path'));
        $this->request->expects($this->any())->method('getParam')->with('___from_store')
            ->will($this->returnValue('old-store'));
        $oldStore = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $this->storeManager->expects($this->any())->method('getStore')
            ->will($this->returnValueMap([['old-store', $oldStore], [null, $this->store]]));
        $oldStore->expects($this->any())->method('getId')->will($this->returnValue('old-store-id'));
        $this->store->expects($this->any())->method('getId')->will($this->returnValue('current-store-id'));
        $oldUrlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $oldUrlRewrite->expects($this->any())->method('getEntityType')->will($this->returnValue('entity-type'));
        $oldUrlRewrite->expects($this->any())->method('getEntityId')->will($this->returnValue('entity-id'));
        $oldUrlRewrite->expects($this->any())->method('getRequestPath')->will($this->returnValue('request-path'));
        $urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $urlRewrite->expects($this->any())->method('getRequestPath')->will($this->returnValue('request-path'));

        $this->assertEquals(null, $this->router->match($this->request));
    }

    public function testMatchWithRedirect()
    {
        $this->state->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->store));
        $urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $urlRewrite->expects($this->any())->method('getRedirectType')->will($this->returnValue('redirect-code'));
        $urlRewrite->expects($this->any())->method('getTargetPath')->will($this->returnValue('target-path'));
        $this->urlFinder->expects($this->any())->method('findOneByData')->will($this->returnValue($urlRewrite));
        $this->response->expects($this->once())->method('setRedirect')
            ->with('new-target-path', 'redirect-code');
        $this->url->expects($this->once())->method('getUrl')->with('', array('_direct' => 'target-path'))
            ->will($this->returnValue('new-target-path'));
        $this->request->expects($this->once())->method('setDispatched')->with(true);
        $this->actionFactory->expects($this->once())->method('create')
            ->with('Magento\Framework\App\Action\Redirect', ['request' => $this->request]);

        $this->router->match($this->request);
    }

    public function testMatch()
    {
        $this->state->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->store));
        $urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $urlRewrite->expects($this->any())->method('getRedirectType')->will($this->returnValue(0));
        $urlRewrite->expects($this->any())->method('getTargetPath')->will($this->returnValue('target-path'));
        $this->urlFinder->expects($this->any())->method('findOneByData')->will($this->returnValue($urlRewrite));
        $this->request->expects($this->once())->method('setPathInfo')->with('/target-path');
        $this->actionFactory->expects($this->once())->method('create')
            ->with('Magento\Framework\App\Action\Forward', ['request' => $this->request]);

        $this->router->match($this->request);
    }
}
