<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Section;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Section\Converter
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Core\Model\Config\Section\Converter();
    }

    public function testConvert()
    {
        $data = array(
            'some/config/path1' => 'value1',
            'some/config/path2' => 'value2',
        );
        $expectedResult = array(
            'some' => array(
                'config' => array(
                    'path1' => 'value1',
                    'path2' => 'value2',
                ),
            ),
        );
        $this->assertEquals($expectedResult, $this->_model->convert($data));
    }
}
