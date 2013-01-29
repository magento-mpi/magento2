<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Payments_DrawerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Drawer Block
     *
     * @var Mage_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer
     */
    protected $_drawerBlock;

    /**
     * Config data array, used in configCallback method
     *
     * @var array
     */
    protected $_configData;

    public function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $config = $this->getMock('Mage_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $config->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(array($this, 'configCallback')));

        $arguments = array(
            'storeConfig' => $config,
            'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false),
            'linkTracker' => $this->getMock('Mage_Launcher_Model_LinkTracker', array(), array(), '', false)
        );

        $this->_drawerBlock = $objectManagerHelper->getBlock(
            'Mage_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer',
            $arguments
        );

        $this->_configData = array();
    }

    public function tearDown()
    {
        unset($this->_drawerBlock);
        unset($this->_configData);
    }

    /**
     * Callback function for getConfig method
     *
     * @param string $param
     * @return string
     */
    public function configCallback($param)
    {
        return isset($this->_configData[$param]) ? $this->_configData[$param] : '';
    }

    /**
     * @dataProvider getConfigValueDataProvider
     * @param array $configData
     * @param string $sourceValue
     * @param string $expectedValue
     */
    public function testGetConfigValue($configData, $sourceValue, $expectedValue)
    {
        $this->_configData = $configData;
        $result = $this->_drawerBlock->getConfigValue($sourceValue);

        $this->assertEquals($result, $expectedValue);
    }

    /**
     * Data provider for testGetConfigValue method
     *
     * @return array
     */
    public function getConfigValueDataProvider()
    {
        return array(
            array(array('paypal/general/business_account' => 'user'), 'paypal/general/business_account', 'user'),
            array(array(), 'paypal/general/business_account', ''),
        );
    }

    /**
     * @dataProvider getObscuredValueDataProvider
     * @param array $configData
     * @param string $sourceValue
     * @param string $expectedValue
     */
    public function testGetObscuredValue($configData, $sourceValue, $expectedValue)
    {
        $this->_configData = $configData;
        $result = $this->_drawerBlock->getObscuredValue($sourceValue);

        $this->assertEquals($result, $expectedValue);
    }

    /**
     * Data provider for testGetObscuredValue method
     *
     * @return array
     */
    public function getObscuredValueDataProvider()
    {
        return array(
            array(array('payment/payflow_link/pwd' => 'password'), 'payment/payflow_link/pwd', '******'),
            array(array(), 'payment/payflow_link/pwd', ''),
        );
    }
}
