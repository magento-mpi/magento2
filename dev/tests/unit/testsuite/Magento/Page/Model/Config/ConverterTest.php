<?php
/**
 * \Magento\Page\Model\Config\Converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Page\Model\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Page\Model\Config\Converter
     */
    protected $_model;

    /** @var  array */
    protected $_targetArray;

    public function setUp()
    {
        $this->_model = new \Magento\Page\Model\Config\Converter();
    }

    public function testConvert()
    {
        $dom = new \DOMDocument();
        $xmlFile = __DIR__ . '/_files/page_layouts.xml';
        $dom->loadXML(file_get_contents($xmlFile));

        $expectedResult = array(
            'empty' => array(
                'label' => 'Empty',
                'code' => 'empty',
                'template' => 'empty.phtml',
                'layout_handle' => 'page_empty',
                'is_default' => 0
            ),
            'one_column' => array(
                'label' => '1 column',
                'code' => 'one_column',
                'template' => '1column.phtml',
                'layout_handle' => 'page_one_column',
                'is_default' => 1
            ),
        );
        $this->assertEquals($expectedResult, $this->_model->convert($dom), '', 0, 20);
    }
}
