<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Import_Image_Validator_FileNameTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $pattern
     * @param int $lengthLimit
     * @param string $value
     * @param bool $expected
     * @dataProvider dataProviderForTestIsValid
     */
    public function testIsValidWithSetOptionsThroughConstruct($pattern, $lengthLimit, $value, $expected)
    {
        $validator = new Saas_ImportExport_Model_Import_Image_Validator_FileName(array(
            'pattern' => $pattern,
            'lengthLimit' => $lengthLimit,
        ));

        $this->assertEquals($expected, $validator->isValid($value));
    }

    /**
     * @param string $pattern
     * @param int $lengthLimit
     * @param string $value
     * @param bool $expected
     * @dataProvider dataProviderForTestIsValid
     */
    public function testIsValidWithSetOptionsThroughSetters($pattern, $lengthLimit, $value, $expected)
    {
        $validator = new Saas_ImportExport_Model_Import_Image_Validator_FileName(array(
            'pattern' => '/(.*)/',
            'lengthLimit' => 0,
        ));
        $validator->setPattern($pattern);
        $validator->setLengthLimit($lengthLimit);

        $this->assertEquals($expected, $validator->isValid($value));
    }

    /**
     * @return array
     */
    public function dataProviderForTestIsValid()
    {
        return array(
            array('/\d+/', 255, 'name_with_alpha', false),
            array('/\s+/', 1, 'name_with_alpha', false),
            array('/\s+/', 2, 'name_with_alpha/1', false), // check basename
            array('/[\s_]+/', 255, 'name_with_alpha', true),
            array('/[\s_]+/', 0, 'name_with_alpha', true),
        );
    }

    /**
     * @param string $pattern
     * @param int $lengthLimit
     * @dataProvider dataProviderForTestSetWrongParams
     * @expectedException InvalidArgumentException
     */
    public function testSetWrongParamsThroughConstruct($pattern, $lengthLimit)
    {
        new Saas_ImportExport_Model_Import_Image_Validator_FileName(array(
            'pattern' => $pattern,
            'lengthLimit' => $lengthLimit,
        ));
    }

    /**
     * @param string $pattern
     * @param int $lengthLimit
     * @dataProvider dataProviderForTestSetWrongParams
     * @expectedException InvalidArgumentException
     */
    public function testSetWrongParamsThroughSetters($pattern, $lengthLimit)
    {
        $validator = new Saas_ImportExport_Model_Import_Image_Validator_FileName(array(
            'pattern' => '/(.*)/',
            'lengthLimit' => 0,
        ));
        $validator->setPattern($pattern);
        $validator->setLengthLimit($lengthLimit);
    }

    /**
     * @return array
     */
    public function dataProviderForTestSetWrongParams()
    {
        return array(
            array('', 255),
            array(array(), 255),
            array('/\s+/', -1),
            array('/\s+/', array()),
        );
    }

    public function testClearingMessagesAfterNewIsValidCall()
    {
        $validator = new Saas_ImportExport_Model_Import_Image_Validator_FileName(array(
            'pattern' => '/\d+/',
            'lengthLimit' => 0,
        ));

        $validator->isValid('wrong_value');
        $this->assertCount(1, $validator->getMessages());

        $validator->isValid('wrong_value_another');
        $this->assertCount(1, $validator->getMessages());
    }
}
