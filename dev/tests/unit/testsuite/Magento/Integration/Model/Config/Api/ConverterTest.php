<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Config\Api;

use \Magento\Integration\Model\Config\Api\Converter;

/**
 * Test for conversion of integration API XML config into array representation.
 */
class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Converter
     */
    protected $_model;

    public function setUp()
    {
        $this->markTestIncomplete('Functionality is not implemented yet');
        $this->_model = new Converter();
    }

    public function testConvert()
    {
        $inputData = new \DOMDocument();
        $inputData->load(__DIR__ . '/_files/api.xml');
        $expectedResult = require __DIR__ . '/_files/api.php';
        $this->assertEquals($expectedResult, $this->_model->convert($inputData));
    }
}
