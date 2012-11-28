<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class EntryPointTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $requestData
     * @param array $inputConfig
     * @param array $expectedConfig
     *
     * @dataProvider entryPointDataProvider
     */
    public function testEntryPoint($requestData = array(), $inputConfig = array(), $expectedConfig = array())
    {
        $config = $inputConfig;
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array('getServer'));
        $request->expects($this->any())
            ->method('getServer')
            ->will($this->returnValue($requestData));

        /** @var $app EntryPoint|PHPUnit_Framework_MockObject_MockObject */
        $app = $this->getMock('EntryPoint', array('_run'), array($request));
        $app->expects($this->once())
            ->method('_run')
            ->with($this->anything(), $this->anything(), $expectedConfig);

        $app(serialize($config));
    }

    /**
     * @return array
     */
    public function entryPointDataProvider()
    {
        return array(
            'only request data' => array(
                array(
                    Mage_Core_Model_App_Options::OPTION_LOCAL_CONFIG_EXTRA_FILE => 'request_file',
                ),
                array(),
                array(
                    Mage_Core_Model_Config::OPTION_LOCAL_CONFIG_EXTRA_DATA => array(),
                    Mage_Core_Model_Config::OPTION_LOCAL_CONFIG_EXTRA_FILE => 'request_file',
                ),
            ),
            'only local data' => array(
                array(),
                array(
                    'base_config' => array('base_opt' => 'local_base_value'),
                    'options' => array(Mage_Core_Model_App_Options::OPTION_LOCAL_CONFIG_EXTRA_FILE => 'local_file'),
                ),
                array(
                    Mage_Core_Model_Config::OPTION_LOCAL_CONFIG_EXTRA_DATA => array('base_opt' => 'local_base_value'),
                    Mage_Core_Model_App_Options::OPTION_LOCAL_CONFIG_EXTRA_FILE => 'local_file',
                ),
            ),
            'both request and local data' => array(
                array(
                    Mage_Core_Model_App_Options::OPTION_LOCAL_CONFIG_EXTRA_FILE => 'request_file',
                ),
                array(
                    'base_config' => array('base_opt' => 'local_base_value'),
                    'options' => array(Mage_Core_Model_Config::OPTION_LOCAL_CONFIG_EXTRA_FILE => 'local_file'
                    ),
                ),
                array(
                    Mage_Core_Model_Config::OPTION_LOCAL_CONFIG_EXTRA_DATA => array('base_opt' => 'local_base_value'),
                    Mage_Core_Model_Config::OPTION_LOCAL_CONFIG_EXTRA_FILE => 'local_file',
                ),
            ),
       );
    }
}
