<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\DesignEditor\Controller\Varien\Router;

class StandardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test area code
     */
    const AREA_CODE = 'frontend';

    /**
     * Test VDE front name prefix
     */
    const VDE_FRONT_NAME = 'test_front_name/test_mode_type/test_theme_id';

    /**
     * Test VDE configuration data
     */
    const VDE_CONFIGURATION_DATA = 'vde_config_data';

    /**#@+
     * Test path and host
     */
    const TEST_PATH = '/customer/account';
    const TEST_HOST = 'http://test.domain';
    /**#@-*/

    /**
     * @var \Magento\DesignEditor\Controller\Varien\Router\Standard
     */
    protected $_model;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param bool $isVde
     * @param bool $isLoggedIn
     * @param bool $isConfiguration
     * @param array $routers
     * @param string|null $matchedValue
     *
     * @dataProvider matchDataProvider
     */
    public function testMatch(
        \Magento\App\RequestInterface $request,
        $isVde,
        $isLoggedIn,
        $isConfiguration,
        array $routers = array(),
        $matchedValue = null
    ) {
        $this->_model = $this->_prepareMocksForTestMatch($request, $isVde, $isLoggedIn, $isConfiguration, $routers);

        $this->assertEquals($matchedValue, $this->_model->match($request));
        if ($isVde && $isLoggedIn) {
            $this->assertEquals(self::TEST_PATH, $request->getPathInfo());
        }
    }

    /**
     * Data provider for testMatch
     *
     * @return array
     */
    public function matchDataProvider()
    {
        $vdeUrl    = self::TEST_HOST . '/' . self::VDE_FRONT_NAME . self::TEST_PATH;
        $notVdeUrl = self::TEST_HOST . self::TEST_PATH;

        $excludedRouters = array(
            'admin' => 'admin router',
            'vde'   => 'vde router',
        );

        // test data to verify routers match logic
        $matchedRequest = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $routerMockedMethods = array('match');

        $matchedController = $this->getMockForAbstractClass(
            'Magento\App\Action\AbstractAction', array(), '', false);

        // method "match" will be invoked for this router because it's first in the list
        $matchedRouter = $this->getMock(
            'Magento\Core\Controller\Varien\Router\Base', $routerMockedMethods, array(), '', false
        );
        $matchedRouter->expects($this->once())
            ->method('match')
            ->with($matchedRequest)
            ->will($this->returnValue($matchedController));

        // method "match" will not be invoked for this router because controller will be found by first router
        $notMatchedRouter = $this->getMock(
            'Magento\Core\Controller\Varien\Router\Base', $routerMockedMethods, array(), '', false
        );
        $notMatchedRouter->expects($this->never())
            ->method('match');

        $matchedRouters = array_merge($excludedRouters,
            array('matched' => $matchedRouter, 'not_matched' => $notMatchedRouter)
        );

        $requestMockNoVde = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $requestMockNoVde->expects($this->any())
            ->method('getPathInfo')
            ->will($this->returnValue($notVdeUrl));

        $requestMockVde = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $requestMockVde->expects($this->any())
            ->method('getPathInfo')
            ->will($this->returnValue($vdeUrl));

        return array(
            'not vde request' => array(
                '$request'         => $requestMockNoVde,
                '$isVde'           => false,
                '$isLoggedIn'      => true,
                '$isConfiguration' => false,
            ),
            'not logged as admin' => array(
                '$request'         => $requestMockVde,
                '$isVde'           => true,
                '$isLoggedIn'      => false,
                '$isConfiguration' => false,
            ),
            'no matched routers' => array(
                '$request'         => $requestMockVde,
                '$isVde'           => true,
                '$isLoggedIn'      => true,
                '$isConfiguration' => false,
                '$routers'         => $excludedRouters
            ),
            'matched routers' => array(
                '$request'         => $matchedRequest,
                '$isVde'           => true,
                '$isLoggedIn'      => true,
                '$isConfiguration' => true,
                '$routers'         => $matchedRouters,
                '$matchedValue'    => $matchedController,
            ),
        );
    }

    /**
     * @param \Magento\App\RequestInterface $request
     * @param bool $isVde
     * @param bool $isLoggedIn
     * @param bool $isConfiguration
     * @param array $routers
     * @return \Magento\DesignEditor\Controller\Varien\Router\Standard
     */
    protected function _prepareMocksForTestMatch(
        \Magento\App\RequestInterface $request,
        $isVde,
        $isLoggedIn,
        $isConfiguration,
        array $routers
    ) {
        // default mocks - not affected on method functionality
        $objectManager      = $this->getMock('Magento\ObjectManager');
        $helper         = $this->_getHelperMock($isVde);
        $backendSession = $this->_getBackendSessionMock($isVde, $isLoggedIn);
        $stateModel     = $this->_getStateModelMock($routers);
        $configuration  = $this->_getConfigurationMock($isVde, $isLoggedIn, $isConfiguration);
        $callback = function ($name) use ($helper, $backendSession, $stateModel, $configuration) {
            switch ($name) {
                case 'Magento\DesignEditor\Helper\Data':
                    return $helper;
                case 'Magento\Backend\Model\Auth\Session':
                    return $backendSession;
                case 'Magento\DesignEditor\Model\State':
                    return $stateModel;
                case 'Magento\Core\Model\Config':
                    return $configuration;
                default:
                    return null;
            }
        };
        $objectManager->expects($this->any())
            ->method('get')
            ->will($this->returnCallback($callback));

        $frontController = $this->getMock('Magento\App\FrontController',
            array('applyRewrites', 'getRouters'), array(), '', false
        );
        if ($isVde && $isLoggedIn) {
            $frontController->expects($this->once())
                ->method('applyRewrites')
                ->with($request);
            $frontController->expects($this->once())
                ->method('getRouters')
                ->will($this->returnValue($routers));
        }

        $routerListMock = $this->getMock('Magento\App\RouterListInterface');
        $routerListMock->expects($this->any())
            ->method('getRouters')
            ->will($this->returnValue(array()));

        $router = new \Magento\DesignEditor\Controller\Varien\Router\Standard(
            $routerListMock,
            $this->getMock('Magento\App\ActionFactory', array(), array(), '', false),
            $objectManager,
            $this->getMockBuilder('Magento\Filesystem')->disableOriginalConstructor()->getMock(),
            $this->getMock('Magento\Core\Model\App', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Route\Config', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Url\SecurityInfoInterface'),
            $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Config', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Url', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false),
            $this->getMock('Magento\App\State', array(), array(), '', false),
            $this->getMock('Magento\Core\App\Request\RewriteService', array(), array(), '', false),
            'frontend',
            'Magento\Core\Controller\Varien\Action',
            'vde'
        );
        $router->setFront($frontController);
        return $router;
    }

    /**
     * @param bool $isVde
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getHelperMock($isVde)
    {
        $helper = $this->getMock('Magento\DesignEditor\Helper\Data', array('isVdeRequest'), array(), '', false);
        $helper->expects($this->once())
            ->method('isVdeRequest')
            ->will($this->returnValue($isVde));
        return $helper;
    }

    /**
     * @param bool $isVde
     * @param bool $isLoggedIn
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getBackendSessionMock($isVde, $isLoggedIn)
    {
        $backendSession = $this->getMock('Magento\Backend\Model\Auth\Session', array('isLoggedIn'), array(), '', false);
        $backendSession->expects($isVde ? $this->once() : $this->never())
            ->method('isLoggedIn')
            ->will($this->returnValue($isLoggedIn));
        return $backendSession;
    }

    /**
     * @param array $routers
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getStateModelMock(array $routers)
    {
        $stateModel = $this->getMock('Magento\DesignEditor\Model\State', array('update'), array(), '', false);
        if (array_key_exists('matched', $routers)) {
            $stateModel->expects($this->once())
                ->method('update')
                ->with(self::AREA_CODE);
            return $stateModel;
        }
        return $stateModel;
    }

    /**
     * @param bool $isVde
     * @param bool $isLoggedIn
     * @param bool $isConfiguration
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getConfigurationMock($isVde, $isLoggedIn, $isConfiguration)
    {
        $configuration = $this->getMock('Magento\Core\Model\Config', array('getNode'), array(), '', false);
        if ($isVde && $isLoggedIn) {
            $configurationData = null;
            if ($isConfiguration) {
                $configurationData = self::VDE_CONFIGURATION_DATA;
            }
            $configuration->expects($this->at(0))
                ->method('getNode')
                ->with(\Magento\DesignEditor\Model\Area::AREA_VDE)
                ->will($this->returnValue($configurationData));

            if ($isConfiguration) {
                $elementMock = $this->getMock('stdClass', array('extend'), array(), '', false);
                $elementMock->expects($this->once())
                    ->method('extend')
                    ->with(self::VDE_CONFIGURATION_DATA, true);

                $configuration->expects($this->at(1))
                    ->method('getNode')
                    ->with(\Magento\Core\Model\App\Area::AREA_FRONTEND)
                    ->will($this->returnValue($elementMock));
            }
        }
        return $configuration;
    }
}
