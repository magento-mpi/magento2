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

class Mage_Launcher_Model_Storelauncher_Businessinfo_StateResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_Launcher_Model_Storelauncher_Businessinfo_StateResolver::isTileComplete
     * @param string $email
     * @param int $expectedState
     * @dataProvider getHandleSystemConfigChangeData
     */
    public function testHandleSystemConfigChange($email, $expectedState)
    {
        $store = $this->getMock('Mage_Core_Model_Store', array('getConfig'), array(), '', false);

        $store->expects($this->once())
            ->method('getConfig')
            ->with('trans_email/ident_general/email')
            ->will($this->returnValue($email));

        $app = $this->getMock('Mage_Core_Model_App', array('getStore'), array(), '', false);

        $app->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));

        $config = $this->getMock('Mage_Core_Model_Config', array('reinit'), array(), '', false);

        $config->expects($this->once())
            ->method('reinit')
            ->will($this->returnValue(true));

        $model = new Mage_Launcher_Model_Storelauncher_Businessinfo_StateResolver($app, $config);
        $this->assertEquals(
            $expectedState,
            $model->handleSystemConfigChange('trans_email', Mage_Launcher_Model_Tile::STATE_TODO)
        );
    }

    /**
     * Get handle system config change data
     *
     * @return array
     */
    public function getHandleSystemConfigChangeData()
    {
        return array(
            array('owner@example.com', Mage_Launcher_Model_Tile::STATE_TODO),
            array('test@example.com', Mage_Launcher_Model_Tile::STATE_COMPLETE),
            array(null, Mage_Launcher_Model_Tile::STATE_TODO),
        );
    }
}
