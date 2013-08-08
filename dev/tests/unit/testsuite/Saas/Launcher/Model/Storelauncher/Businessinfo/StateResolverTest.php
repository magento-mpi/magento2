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

class Saas_Launcher_Model_Storelauncher_Businessinfo_StateResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Saas_Launcher_Model_Storelauncher_Businessinfo_StateResolver::isTileComplete
     * @param string $email
     * @param int $expectedState
     * @dataProvider getTestDataIsTileComplete
     */
    public function testIsTileComplete($email, $expectedState)
    {
        $stateResolver = $this->_getStateResolverWithEmail($email);
        $this->assertEquals(
            $expectedState,
            $stateResolver->isTileComplete()
        );
    }

    /**
     * @covers Saas_Launcher_Model_Storelauncher_Businessinfo_StateResolver::getPersistentState
     * @param string $email
     * @param int $expectedState
     * @dataProvider getTestData
     */
    public function testGetPersistentState($email, $expectedState)
    {
        $stateResolver = $this->_getStateResolverWithEmail($email);
        $this->assertEquals(
            $expectedState,
            $stateResolver->getPersistentState()
        );
    }

    /**
     * @covers Saas_Launcher_Model_Storelauncher_Businessinfo_StateResolver::isTileComplete
     * @param string $email
     * @param int $expectedState
     * @dataProvider getTestData
     */
    public function testHandleSystemConfigChange($email, $expectedState)
    {
        $stateResolver = $this->_getStateResolverWithEmail($email);
        $this->assertEquals(
            $expectedState,
            $stateResolver->handleSystemConfigChange('trans_email', Saas_Launcher_Model_Tile::STATE_TODO)
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
            array(
                Saas_Launcher_Model_Storelauncher_Businessinfo_StateResolver::DEFAULT_EMAIL_ADDRESS,
                Saas_Launcher_Model_Tile::STATE_TODO
            ),
            array('test@example.com', Saas_Launcher_Model_Tile::STATE_COMPLETE),
            array(null, Saas_Launcher_Model_Tile::STATE_TODO),
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
            array('owner@example.com', false),
            array('test@example.com', true),
            array(null, false),
        );
    }

    /**
     * Get State Resolver with specified General email
     *
     * @param string $email
     * @return Saas_Launcher_Model_Storelauncher_Businessinfo_StateResolver
     */
    protected function _getStateResolverWithEmail($email)
    {
        $store = $this->getMock('Magento_Core_Model_Store', array('getConfig'), array(), '', false);

        $store->expects($this->once())
            ->method('getConfig')
            ->with('trans_email/ident_general/email')
            ->will($this->returnValue($email));

        $app = $this->getMock('Magento_Core_Model_App', array('getStore'), array(), '', false);

        $app->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));

        return new Saas_Launcher_Model_Storelauncher_Businessinfo_StateResolver($app);
    }
}
