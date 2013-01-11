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

class Mage_Launcher_Model_Tile_MinimalStateResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Model_Tile_MinimalStateResolver
     */
    protected $_stateResolver;

    protected function setUp()
    {
        $this->_stateResolver = new Mage_Launcher_Model_Tile_MinimalStateResolver();
    }

    public function testIsTileComplete()
    {
        $this->assertTrue($this->_stateResolver->isTileComplete());
    }

    /**
     * @dataProvider handleSystemConfigChangeDataProvider
     * @param int $currentState
     */
    public function testHandleSystemConfigChange($currentState)
    {
        // Tile is not system-config depended, so this method always has to return current tile state
        $resultState = $this->_stateResolver->handleSystemConfigChange('general', $currentState);
        $this->assertEquals($currentState, $resultState);
    }

    public function handleSystemConfigChangeDataProvider()
    {
        return array(
            array(Mage_Launcher_Model_Tile::STATE_COMPLETE),
            array(Mage_Launcher_Model_Tile::STATE_TODO),
        );
    }
}
