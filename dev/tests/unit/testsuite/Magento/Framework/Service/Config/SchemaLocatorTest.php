<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service\Config;

/**
 * Test for \Magento\Framework\Service\Config\SchemaLocator
 */
class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Service\Config\SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Framework\Service\Config\SchemaLocator();
    }

    public function testGetSchema()
    {
        $expected = str_replace('\\', '/', BP . '/lib/internal/Magento/Framework/Service/etc/data_object.xsd');
        $actual = str_replace('\\', '/', $this->_model->getSchema());
        $this->assertEquals($expected, $actual);
    }

    public function testGetPerFileSchema()
    {
        $actual = str_replace('\\', '/', $this->_model->getPerFileSchema());
        $expected = str_replace('\\', '/', BP . '/lib/internal/Magento/Framework/Service/etc/data_object.xsd');
        $this->assertEquals($expected, $actual);
    }
}
