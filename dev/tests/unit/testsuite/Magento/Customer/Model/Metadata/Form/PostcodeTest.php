<?php
/**
 * test Magento\Customer\Model\Metadata\Form\Postcode
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

class PostcodeTest extends AbstractFormTestCase
{
    /** @var \Magento\Framework\Stdlib\String */
    /** @var  \Magento\Directory\Helper\Data */
    protected $stringHelper;
    protected $_directoryData;

    protected function setUp()
    {
        parent::setUp();
        $this->stringHelper = new \Magento\Framework\Stdlib\String();

        $this->_directoryData = $this->getMockBuilder(
            '\Magento\Directory\Helper\Data'
        )->disableOriginalConstructor()->setMethods(
            array('getCountriesWithOptionalZip')
        )->getMock();

        $this->_directoryData->expects(
            $this->any()
        )->method(
            'getCountriesWithOptionalZip'
        )->will(
            $this->returnValue(['KN', 'IE'])
        );

        $this->attributeMetadataMock->expects($this->any())->method('isRequired')->will($this->returnValue(true));
    }

    /**
     * Create an instance of the class that is being tested
     *
     * @param string|int|bool|null $value The value undergoing testing by a given test
     * @return Text
     */
    protected function getClass($value)
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
            $this->_directoryData
        );
    }

    /**
     * @param string|int $value
     * @param bool $expected text output
     * @dataProvider validateValueRequiredDataProvider
     */
    public function testValidateValueRequired($value, $expected)
    {
        $sut = $this->getClass($value);
        $sut->setExtractedData(['country_id' => 'UK']);
        $actual = $sut->validateValue($value);
        $this->assertEquals($expected, $actual);
    }

    public function validateValueRequiredDataProvider()
    {
        return [
            [123321, true],
            ['WA11 0JD', true],
            ['01001', true],
            ['', [0 => '"" is a required value.']]
        ];
    }

    /**
     * @param string|int $value
     * @param bool $expected
     * @dataProvider validateValueNotrequiredDataProvider
     */
    public function testValidateValueNotRequired($value, $expected)
    {
        $sut = $this->getClass($value);
        $sut->setExtractedData(['country_id' => 'KN']);
        $actual = $sut->validateValue($value);
        $this->assertEquals($expected, $actual);
    }

    public function validateValueNotRequiredDataProvider()
    {
        return [
            ['', true],
            [123, true],
            ['WA11 0JD', true],
            ['01001', true]
        ];
    }
}
