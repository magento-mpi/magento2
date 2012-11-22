<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_App_OptionsTest extends PHPUnit_Framework_TestCase
{
    public function testGetRunCode()
    {
        $model = new Mage_Core_Model_App_Options(array());
        $this->assertEmpty($model->getRunCode());

        $model = new Mage_Core_Model_App_Options(array(Mage_Core_Model_App_Options::OPTION_APP_RUN_CODE => 'test'));
        $this->assertEquals('test', $model->getRunCode());
    }

    public function testGetRunType()
    {
        $model = new Mage_Core_Model_App_Options(array());
        $this->assertEquals(Mage_Core_Model_App_Options::APP_RUN_TYPE_STORE, $model->getRunType());

        $runType = Mage_Core_Model_App_Options::APP_RUN_TYPE_WEBSITE;
        $model = new Mage_Core_Model_App_Options(array(Mage_Core_Model_App_Options::OPTION_APP_RUN_TYPE => $runType));
        $this->assertEquals($runType, $model->getRunType());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage run type "invalid" is not recognized, supported values: "store", "group", "website"
     */
    public function testGetRunTypeException()
    {
        new Mage_Core_Model_App_Options(array(Mage_Core_Model_App_Options::OPTION_APP_RUN_TYPE => 'invalid'));
    }

    public function testGetRunOptions()
    {
        $model = new Mage_Core_Model_App_Options(array('ignored_option' => 'ignored value'));
        $this->assertEmpty($model->getRunOptions());

        $extraLocalConfigFile = 'test/local.xml';
        $inputOptions = array(Mage_Core_Model_App_Options::OPTION_LOCAL_CONFIG_EXTRA_FILE => $extraLocalConfigFile);
        $expectedRunOptions = array(Mage_Core_Model_Config::OPTION_LOCAL_CONFIG_EXTRA_FILE => $extraLocalConfigFile);
        $model = new Mage_Core_Model_App_Options($inputOptions);
        $this->assertEquals($expectedRunOptions, $model->getRunOptions());
    }
}
