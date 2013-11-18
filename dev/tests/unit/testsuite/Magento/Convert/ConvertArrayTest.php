<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Convert;

class ConvertArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Convert\ConvertArray
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Convert\ConvertArray;
    }

    public function testAssocToXml()
    {
        $data = array(
            'one' => 1,
            'two' => array(
                'three' => 3,
                'four' => '4',
            ),
        );
        $result = $this->_model->assocToXml($data);
        $xmlParts = array(
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>',
            '<_><one>1</one><two><three>3</three><four>4</four></two></_>',
            ''
        );
        $expectedResult = implode("\n", $xmlParts);
        $this->assertInstanceOf('SimpleXMLElement', $result);
        $this->assertEquals($expectedResult, $result->asXML());
    }

    /**
     * @param array $array
     * @param string $rootName
     * @expectedException \Magento\Exception
     * @dataProvider assocToXmlExceptionDataProvider
     */
    public function testAssocToXmlException($array, $rootName = '_')
    {
        $this->_model->assocToXml($array, $rootName);
    }

    /**
     * @return array
     */
    public function assocToXmlExceptionDataProvider()
    {
        return array(
            array(array(), ''),
            array(array(), 0),
            array(array(1, 2, 3)),
            array(array('root' => 1), 'root'),
        );
    }
}
