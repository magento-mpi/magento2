<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model;

/**
 * @magentoAppArea frontend
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\FullPageCache\Model\Observer
     */
    protected $_observer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cookie;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\App\Cache\StateInterface $cacheState */
        $cacheState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\App\Cache\StateInterface'
        );
        $cacheState->setEnabled('full_page', true);
        $this->_cookie = $this->getMock(
            'Magento\FullPageCache\Model\Cookie',
            array('set', 'delete', 'updateCustomerCookies'),
            array(),
            '',
            false,
            false
        );

        $this->_observer = $objectManager->create(
            'Magento\FullPageCache\Model\Observer',
            array('cookie' => $this->_cookie)
        );
    }

    public function testProcessPreDispatchCanProcessRequest()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\TestFramework\Request');
        $response = $objectManager->get('Magento\TestFramework\Response');

        $request->setRouteName('catalog');
        $request->setControllerName('product');
        $request->setActionName('view');

        $observerData = new \Magento\Event\Observer();
        $arguments = array('request' => $request, 'response' => $response);
        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\App\Action\Context',
            $arguments
        );
        $observerData->setEvent(
            new \Magento\Event(
                array(
                    'controller_action' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
                        'Magento\App\Action\Action',
                        array('context' => $context)
                    )
                )
            )
        );

        $this->_cookie->expects($this->once())->method('updateCustomerCookies');

        /** @var $cacheState \Magento\App\Cache\StateInterface */
        $cacheState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\App\Cache\StateInterface'
        );

        $cacheState->setEnabled(\Magento\View\Element\AbstractBlock::CACHE_GROUP, true);

        /** @var $session \Magento\Catalog\Model\Session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Session');
        $session->setParamsMemorizeDisabled(false);

        $this->_observer->processPreDispatch($observerData);

        $this->assertFalse($cacheState->isEnabled(\Magento\View\Element\AbstractBlock::CACHE_GROUP));
        $this->assertTrue(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Catalog\Model\Session'
            )->getParamsMemorizeDisabled()
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testProcessPreDispatchCannotProcessRequest()
    {
        /** @var $restriction \Magento\FullPageCache\Model\Processor\RestrictionInterface */
        $restriction = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\FullPageCache\Model\Processor\RestrictionInterface'
        );
        $restriction->setIsDenied();
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\TestFramework\Request');
        $observerData = new \Magento\Event\Observer();
        $arguments = array(
            'request' => $request,
            'response' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\TestFramework\Response'
            )
        );
        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\App\Action\Context',
            $arguments
        );
        $observerData->setEvent(
            new \Magento\Event(
                array(
                    'controller_action' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
                        'Magento\App\Action\Action',
                        array('context' => $context)
                    )
                )
            )
        );
        $this->_cookie->expects($this->once())->method('updateCustomerCookies');

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Catalog\Model\Session'
        )->setParamsMemorizeDisabled(
            true
        );
        $this->_observer->processPreDispatch($observerData);
        $this->assertFalse(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Catalog\Model\Session'
            )->getParamsMemorizeDisabled()
        );
    }

    public function testSetNoCacheCookie()
    {
        $this->_cookie->expects(
            $this->once()
        )->method(
            'set'
        )->with(
            \Magento\FullPageCache\Model\Processor\RestrictionInterface::NO_CACHE_COOKIE
        );
        $this->_observer->setNoCacheCookie(new \Magento\Event\Observer());
    }

    public function testDeleteNoCacheCookie()
    {
        $this->_cookie->expects(
            $this->once()
        )->method(
            'set'
        )->with(
            \Magento\FullPageCache\Model\Processor\RestrictionInterface::NO_CACHE_COOKIE,
            null,
            0
        );
        $this->_observer->deleteNoCacheCookie(new \Magento\Event\Observer());
    }
}
