<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config;

class ValidationStateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $appMode
     * @param boolean $expectedResult
     * @dataProvider isValidatedDataProvider
     */
    public function testIsValidated($appMode, $expectedResult)
    {
        $model = new \Magento\App\Arguments\ValidationState($appMode);
        $this->assertEquals($model->isValidated(), $expectedResult);
    }

    /**
     * @return array
     */
    public function isValidatedDataProvider()
    {
        return array(
            array(\Magento\App\State::MODE_DEVELOPER, true),
            array(\Magento\App\State::MODE_DEFAULT, false),
            array(\Magento\App\State::MODE_PRODUCTION, false)
        );
    }
}
