<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Controller_Varien_Router_StandardTest extends PHPUnit_Framework_TestCase
{
    /**
     * Fake controller instance
     */
    const TEST_CONTROLLER = 'test controller instance';

    /**
     * Test area code
     */
    const AREA_CODE = 'frontend';

    /**
     * Test VDE front name prefix
     */
    const VDE_FRONT_NAME = 'test_front_name';

    /**#@+
     * Test path and host
     */
    const TEST_PATH = '/customer/account';
    const TEST_HOST = 'http://test.domain';
    /**#@-*/

    /**
     * @var Mage_DesignEditor_Controller_Varien_Router_Standard
     */
    protected $_model;

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param bool $isVde
     * @param bool $isLoggedIn
     * @param array $routers
     * @param string|null $matchedValue
     *
     * @dataProvider matchDataProvider
     */
    public function testMatch(
        Mage_Core_Controller_Request_Http $request,
        $isVde,
        $isLoggedIn,
        array $routers = array(),
        $matchedValue = null
    ) {
        $this->_model = $this->_prepareMocksForTestMatch($request, $isVde, $isLoggedIn, $routers);

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

        $silencedMethods = array('_canBeStoreCodeInUrl');
        $excludedRouters = array(
            'admin' => 'admin router',
            'vde'   => 'vde router',
        );

        // test data to verify routers match logic
        $matchedRequest = $this->getMock('Mage_Core_Controller_Request_Http', $silencedMethods, array($vdeUrl));
        $routerMockedMethods = array('match');

        // method "match" will be invoked for this router because it's first in the list
        $matchedRouter = $this->getMock(
            'Mage_Core_Controller_Varien_Router_Base', $routerMockedMethods, array(), '', false
        );
        $matchedRouter->expects($this->once())
            ->method('match')
            ->with($matchedRequest)
            ->will($this->returnValue(self::TEST_CONTROLLER));

        // method "match" will not be invoked for this router because controller will be found by first router
        $notMatchedRouter = $this->getMock(
            'Mage_Core_Controller_Varien_Router_Base', $routerMockedMethods, array(), '', false
        );
        $notMatchedRouter->expects($this->never())
            ->method('match');

        $matchedRouters = array_merge($excludedRouters,
            array('matched' => $matchedRouter, 'not_matched' => $notMatchedRouter)
        );

        return array(
            'not vde request' => array(
                '$request'    => $this->getMock(
                    'Mage_Core_Controller_Request_Http', $silencedMethods, array($notVdeUrl)
                ),
                '$isVde'      => false,
                '$isLoggedIn' => true
            ),
            'not logged as admin' => array(
                '$request'    => $this->getMock(
                    'Mage_Core_Controller_Request_Http', $silencedMethods, array($vdeUrl)
                ),
                '$isVde'      => true,
                '$isLoggedIn' => false
            ),
            'no matched routers' => array(
                '$request'    => $this->getMock(
                    'Mage_Core_Controller_Request_Http', $silencedMethods, array($vdeUrl)
                ),
                '$isVde'      => true,
                '$isLoggedIn' => true,
                '$routers'    => $excludedRouters
            ),
            'matched routers' => array(
                '$request'     => $matchedRequest,
                '$isVde'       => true,
                '$isLoggedIn'  => true,
                '$routers'     => $matchedRouters,
                '$matchedValue' => self::TEST_CONTROLLER,
            ),
        );
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param bool $isVde
     * @param bool $isLoggedIn
     * @param array $routers
     * @return Mage_DesignEditor_Controller_Varien_Router_Standard
     */
    protected function _prepareMocksForTestMatch(
        Mage_Core_Controller_Request_Http $request,
        $isVde,
        $isLoggedIn,
        array $routers
    ) {
        // default mocks - not affected on method functionality
        $controllerFactory  = $this->getMock('Mage_Core_Controller_Varien_Action_Factory', array(), array(), '', false);
        $objectManager      = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $testArea           = 'frontend';
        $testBaseController = 'Mage_Core_Controller_Varien_Action';

        $helper = $this->getMock('Mage_DesignEditor_Helper_Data', array('getFrontName'), array(), '', false);
        $helper->expects($this->atLeastOnce())
            ->method('getFrontName')
            ->will($this->returnValue(self::VDE_FRONT_NAME));

        $backendSession = $this->getMock('Mage_Backend_Model_Auth_Session', array('isLoggedIn'), array(), '', false);
        $backendSession->expects($isVde ? $this->once() : $this->never())
            ->method('isLoggedIn')
            ->will($this->returnValue($isLoggedIn));

        $frontController = $this->getMock('Mage_Core_Controller_Varien_Front',
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

        $layoutFactory = $this->getMock('Mage_Core_Model_Layout_Factory', array('createLayout'), array(), '', false);
        if (array_key_exists('matched', $routers)) {
            $layoutFactory->expects($this->once())
                ->method('createLayout')
                ->with(
                    array('area' => self::AREA_CODE),
                    Mage_DesignEditor_Controller_Varien_Router_Standard::LAYOUT_CLASS_NAME
                );
        }

        $router = new Mage_DesignEditor_Controller_Varien_Router_Standard(
            $controllerFactory, $objectManager, $testArea, $testBaseController, $backendSession, $helper, $layoutFactory
        );
        $router->setFront($frontController);
        return $router;
    }
}
