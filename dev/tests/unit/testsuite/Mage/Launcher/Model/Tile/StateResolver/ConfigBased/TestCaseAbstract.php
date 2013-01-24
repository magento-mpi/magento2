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

abstract class Mage_Launcher_Model_Tile_StateResolver_ConfigBased_TestCaseAbstract
    extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public abstract function isTileCompleteDataProvider();

    /**
     * @param Mage_Core_Model_App $app
     * @param Mage_Core_Model_Config $config
     * @return Mage_Launcher_Model_Tile_StateResolver
     */
    protected abstract function _getStateResolverInstance(Mage_Core_Model_App $app, Mage_Core_Model_Config $config);

    /**
     * @dataProvider handleSystemConfigChangeDataProvider
     * @param int $currentState
     */
    public function testHandleSystemConfigChange($currentState)
    {
        $app = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $config = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $stateResolver = $this->_getStateResolverInstance($app, $config);
        // Tile is not system-config depended, so this method always has to return current tile state
        $resultState = $stateResolver->handleSystemConfigChange('general', $currentState);
        $this->assertEquals($currentState, $resultState);
    }

    public function handleSystemConfigChangeDataProvider()
    {
        return array(
            array(Mage_Launcher_Model_Tile::STATE_COMPLETE),
            array(Mage_Launcher_Model_Tile::STATE_TODO),
        );
    }

    /**
     * @param array $configSettings
     * @param boolean $expectedResult
     * @dataProvider isTileCompleteDataProvider
     */
    public function testIsTileComplete(array $configSettings, $expectedResult)
    {
        $stateResolver = $this->_getStateResolverForIsTileCompleteTest($configSettings);
        $this->assertEquals(
            $expectedResult,
            $stateResolver->isTileComplete()
        );
    }

    /**
     * @param array $configSettings
     * @param boolean $isTileComplete
     * @dataProvider isTileCompleteDataProvider
     */
    public function testGetPersistentState(array $configSettings, $isTileComplete)
    {
        $stateResolver = $this->_getStateResolverForIsTileCompleteTest($configSettings);
        $expectedResult = ($isTileComplete)
            ? Mage_Launcher_Model_Tile::STATE_COMPLETE
            : Mage_Launcher_Model_Tile::STATE_TODO;
        $this->assertEquals(
            $expectedResult,
            $stateResolver->getPersistentState()
        );
    }

    /**
     * Retrieve State Resolver instance for isTileComplete test
     *
     * @param array $configSettings
     * @return Mage_Launcher_Model_Tile_StateResolver
     */
    protected function _getStateResolverForIsTileCompleteTest(array $configSettings)
    {
        $store = $this->getMock('Mage_Core_Model_Store', array('getConfig'), array(), '', false);

        // Mock getConfig() call
        $store->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(
            function ($configPath) use ($configSettings) {
                return isset($configSettings[$configPath]) ? $configSettings[$configPath] : null;
            }
        ));

        // Create mock object of Application
        $app = $this->getMock('Mage_Core_Model_App', array('getStore'), array(), '', false);
        $app->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        // Create mock object of Configuration
        $config = $this->getMock('Mage_Core_Model_Config', array('reinit'), array(), '', false);
        $config->expects($this->once())
            ->method('reinit')
            ->will($this->returnValue($config));

        return $this->_getStateResolverInstance($app, $config);
    }
}
