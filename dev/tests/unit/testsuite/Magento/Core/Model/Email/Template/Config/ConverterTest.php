<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Email\Template\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Email\Template\Config\Converter
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new \Magento\Core\Model\Email\Template\Config\Converter();
    }

    public function testConvert()
    {
        $inputData = new \DOMDocument();
        $inputData->load(__DIR__ . '/_files/email_templates_merged.xml');
        $expectedResult = require __DIR__ . '/_files/email_templates_merged.php';
        $this->assertEquals($expectedResult, $this->_model->convert($inputData));
    }
}
