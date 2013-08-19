<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_ValidationStateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $appMode
     * @param boolean $expectedResult
     * @dataProvider isValidatedDataProvider
     */
    public function testIsValidated($appMode, $expectedResult)
    {
        $appStateModel = new Mage_Core_Model_App_State($appMode);
        $model = new Mage_Core_Model_Config_ValidationState($appStateModel);
        $this->assertEquals($model->isValidated(), $expectedResult);
    }

    /**
     * @return array
     */
    public function isValidatedDataProvider()
    {
        return array(
            array(
                Mage_Core_Model_App_State::MODE_DEVELOPER,
                true
            ),
            array(
                Mage_Core_Model_App_State::MODE_DEFAULT,
                false
            ),
            array(
                Mage_Core_Model_App_State::MODE_PRODUCTION,
                false
            ),
        );
    }
}