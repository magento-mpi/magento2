<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_Promotestore_Googleanalytics_StateResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_Launcher_Model_Promotestore_Googleanalytics_StateResolver::isTileComplete
     * @param string $accountId
     * @param int $expectedState
     * @dataProvider getTestDataIsTileComplete
     */
    public function testIsTileComplete($accountId, $expectedState)
    {
        $stateResolver = $this->_getStateResolverWithEmail($accountId);
        $this->assertEquals(
            $expectedState,
            $stateResolver->isTileComplete()
        );
    }

    /**
     * @covers Mage_Launcher_Model_Promotestore_Googleanalytics_StateResolver::getPersistentState
     * @param string $accountId
     * @param int $expectedState
     * @dataProvider getTestData
     */
    public function testGetPersistentState($accountId, $expectedState)
    {
        $stateResolver = $this->_getStateResolverWithEmail($accountId);
        $this->assertEquals(
            $expectedState,
            $stateResolver->getPersistentState()
        );
    }

    /**
     * @covers Mage_Launcher_Model_Promotestore_Googleanalytics_StateResolver::handleSystemConfigChange
     * @param string $accountId
     * @param int $expectedState
     * @dataProvider getTestData
     */
    public function testHandleSystemConfigChange($accountId, $expectedState)
    {
        $stateResolver = $this->_getStateResolverWithEmail($accountId);
        $this->assertEquals(
            $expectedState,
            $stateResolver->handleSystemConfigChange('google', Mage_Launcher_Model_Tile::STATE_TODO)
        );
    }

    /**
     * Data provider for State Resolver
     *
     * @return array
     */
    public function getTestData()
    {
        return array(
            array('', Mage_Launcher_Model_Tile::STATE_TODO),
            array(null, Mage_Launcher_Model_Tile::STATE_TODO),
            array('accountId', Mage_Launcher_Model_Tile::STATE_COMPLETE),
        );
    }

    /**
     * Data provider for State Resolver isComplete method
     *
     * @return array
     */
    public function getTestDataIsTileComplete()
    {
        return array(
            array('accountId', true),
            array('', false),
            array(null, false),
        );
    }

    /**
     * Get State Resolver with specified accountId
     *
     * @param string $accountId
     * @return Mage_Launcher_Model_Promotestore_Googleanalytics_StateResolver
     */
    protected function _getStateResolverWithEmail($accountId)
    {
        $store = $this->getMock('Mage_Core_Model_Store', array('getConfig'), array(), '', false);

        $store->expects($this->once())
            ->method('getConfig')
            ->with('google/analytics/account')
            ->will($this->returnValue($accountId));

        $app = $this->getMock('Mage_Core_Model_App', array('getStore'), array(), '', false);

        $app->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));

        $config = $this->getMock('Mage_Core_Model_Config', array('reinit'), array(), '', false);

        $config->expects($this->once())
            ->method('reinit')
            ->will($this->returnValue(true));

        return new Mage_Launcher_Model_Promotestore_Googleanalytics_StateResolver($app, $config);
    }
}
