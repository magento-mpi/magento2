<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Resource\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_expected;

    /**
     * @var \Magento\App\Resource\Config\SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_expected = str_replace('\\', '/', BP) . '/lib/internal/Magento/App/etc/resources.xsd';
        $this->_model = new \Magento\App\Resource\Config\SchemaLocator();
    }

    public function testGetSchema()
    {
        $this->assertEquals($this->_expected, str_replace('\\', '/', $this->_model->getSchema()));
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals($this->_expected, str_replace('\\', '/', $this->_model->getPerFileSchema()));
    }
}
