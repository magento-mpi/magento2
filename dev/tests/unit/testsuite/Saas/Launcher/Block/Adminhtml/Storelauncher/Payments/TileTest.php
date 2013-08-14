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

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Tile
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_TileTest extends PHPUnit_Framework_TestCase
{
    /**
     * Data array, used in configCallback method
     *
     * @var array
     */
    protected $_data;

    /**
     * Store Config Mock object
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_config;

    public function setUp()
    {
        $this->_config = $this->getMock('Magento_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $this->_config->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(array($this, 'configCallback')));
    }

    /**
     * @dataProvider getConfiguredMethodsDataProvider
     * @param $configData
     * @param $expectedData
     */
    public function testGetConfiguredMethods($configData, $expectedData)
    {
        $this->_data = $configData;

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $arguments = array(
            'storeConfig' => $this->_config,
        );

        /** @var $tileBlock Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Tile */
        $tileBlock = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Tile',
            $arguments
        );

        $result = $tileBlock->getConfiguredMethods();
        $this->assertEquals($expectedData, $result);
    }

    /**
     * Callback function for getConfig method
     *
     * @param string $path
     * @return string
     */
    public function configCallback($path)
    {
        if (!isset($this->_data[$path])) {
            return '';
        }
        return $this->_data[$path];
    }

    public function getConfiguredMethodsDataProvider()
    {
        return array(
            array(
                array('payment' => array(
                    'paypal_express' => array('active' => 1),
                    'payflow_advanced' => array('active' => 1),
                    'cc_save' => array('active' => 1),
                )),
                array(
                    'PayPal Express Checkout',
                    'PayPal Payments Advanced',
                )
            ),
            array(
                array('payment' => array(
                    'paypal_express' => array('active' => 1),
                    'payflow_advanced' => array('active' => 0),
                    'cc_save' => array('active' => 1),
                )),
                array(
                    'PayPal Express Checkout',
                )
            ),
            array(
                array('payment' => array(
                    'paypal_express' => array('active' => 0),
                    'payflow_advanced' => array('active' => 0),
                    'cc_save' => array('active' => 1),
                )),
                array()
            ),
        );
    }
}
