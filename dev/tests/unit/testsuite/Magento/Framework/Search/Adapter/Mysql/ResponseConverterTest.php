<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\TestFramework\Helper\ObjectManager;

class ResponseConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\ResponseConverter
     */
    private $converter;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->converter = $helper->getObject('Magento\Framework\Search\Adapter\Mysql\ResponseConverter');
    }

    public function testConvertToDocument()
    {
        $rawData = [
            ['title' => 'oneTitle', 'description' => 'oneDescription'],
            ['title' => 'twoTitle', 'description' => 'twoDescription']
        ];
        $expectedResult = [
            [
                ['name' => 'title', 'values' => 'oneTitle'],
                ['name' => 'description', 'values' => 'oneDescription']
            ],
            [
                ['name' => 'title', 'values' => 'twoTitle'],
                ['name' => 'description', 'values' => 'twoDescription']
            ]
        ];

        $result = $this->converter->convertToDocument($rawData);

        $this->assertEquals($expectedResult, $result);
    }
}
