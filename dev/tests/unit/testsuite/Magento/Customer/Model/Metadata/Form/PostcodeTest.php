<?php
/**
 * test Magento\Customer\Model\Metadata\Form\Postcode
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

class PostcodeTest extends AbstractFormTestCase
{
    /**
     * @var \Magento\Framework\Stdlib\String
     */
    protected $stringHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryDataMock;

    protected function setUp()
    {
        parent::setUp();
        $this->stringHelper = new \Magento\Framework\Stdlib\String();
        $this->directoryDataMock = $this->getMock('\Magento\Directory\Helper\Data', ['getCountriesWithOptionalZip'], [], '', false);
        $this->directoryDataMock->expects(
            $this->any()
        )->method('getCountriesWithOptionalZip')->will(
            $this->returnValue(['HK', 'IE', 'MO', 'PA', 'GB'])
        );
        $this->attributeMetadataMock->expects(
            $this->any()
        )->method('isRequired')->will(
            $this->returnValue(true)
        );
    }

    /**
     * Create an instance of the class that is being tested
     *
     * @param string|int|bool|null $value The value undergoing testing by a given test
     * @return Text
     */
    protected function getPostcodeInstance($value)
    {
        return new Postcode(
            $this->localeMock,
            $this->loggerMock,
            $this->attributeMetadataMock,
            $this->localeResolverMock,
            $value,
            0,
            false,
            $this->stringHelper,
            $this->directoryDataMock
        );
    }

    /**
     * @param string|int $value
     * @param bool $expectedResult text output
     * @dataProvider validateValueRequiredDataProvider
     */
    public function testValidateValueRequired($value, $extractedData, $expectedResult)
    {
        $model = $this->getPostcodeInstance($value);
        $model->setExtractedData(['country_id' => $extractedData]);
        $actualResult = $model->validateValue($value);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function validateValueRequiredDataProvider()
    {
        return [
            ['WA11 0JD', 'GB', true],
            ['', 'GB', true],
            ['01001', 'UA', true],
            ['', 'UA', [0 => '"" is a required value.']]
        ];
    }
}
