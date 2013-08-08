<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Saas_Launcher_Model_Tile_ConfigBased_StateResolverTestCaseAbstract
    extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public abstract function isTileCompleteDataProvider();

    /**
     * @param Magento_Core_Model_App $app
     * @return Saas_Launcher_Model_Tile_StateResolver
     */
    protected abstract function _getStateResolverInstance(Magento_Core_Model_App $app);

    /**
     * @dataProvider handleSystemConfigChangeDataProvider
     * @param int $currentState
     */
    public function testHandleSystemConfigChange($currentState)
    {
        $app = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $config = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $stateResolver = $this->_getStateResolverInstance($app, $config);
        // Tile is not system-config depended, so this method always has to return current tile state
        $resultState = $stateResolver->handleSystemConfigChange('general', $currentState);
        $this->assertEquals($currentState, $resultState);
    }

    public function handleSystemConfigChangeDataProvider()
    {
        return array(
            array(Saas_Launcher_Model_Tile::STATE_COMPLETE),
            array(Saas_Launcher_Model_Tile::STATE_TODO),
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
            ? Saas_Launcher_Model_Tile::STATE_COMPLETE
            : Saas_Launcher_Model_Tile::STATE_TODO;
        $this->assertEquals(
            $expectedResult,
            $stateResolver->getPersistentState()
        );
    }

    /**
     * Retrieve State Resolver instance for isTileComplete test
     *
     * @param array $configSettings
     * @return Saas_Launcher_Model_Tile_StateResolver
     */
    protected function _getStateResolverForIsTileCompleteTest(array $configSettings)
    {
        $store = $this->getMock('Magento_Core_Model_Store', array('getConfig'), array(), '', false);

        // Mock getConfig() call
        $store->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(
            function ($configPath) use ($configSettings) {
                return isset($configSettings[$configPath]) ? $configSettings[$configPath] : null;
            }
        ));

        // Create mock object of Application
        $app = $this->getMock('Magento_Core_Model_App', array('getStore'), array(), '', false);
        $app->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        return $this->_getStateResolverInstance($app);
    }
}
