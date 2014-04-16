<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Route\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Route\Config\SchemaLocator
     */
    protected $_config;

    protected function setUp()
    {
        $this->_config = new \Magento\App\Route\Config\SchemaLocator();
    }

    public function testGetSchema()
    {
        $actual = $this->_config->getSchema();
        $this->assertContains('routes_merged.xsd', $actual);
    }

    public function testGetPerFileSchema()
    {
        $actual = $this->_config->getPerFileSchema();
        $this->assertContains('routes.xsd', $actual);
    }
}
