<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Acl_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Acl_Config_Reader
     */
    protected $_model;

    public function setUp()
    {
        $files = array(
            realpath(__DIR__) . '/../_files/acl_1.xml',
            realpath(__DIR__) . '/../_files/acl_2.xml'
        );
        $this->_model = new Magento_Acl_Config_Reader($files);
    }

    public function testReaderImplementRequiredInterface()
    {
        $this->assertInstanceOf('Magento_Acl_Config_ReaderInterface', $this->_model);
    }

    public function testGetAclResources()
    {
        /** @var $resources DOMDocument */
        $resources = $this->_model->getAclResources();
        $this->assertNotEmpty($resources);
        $this->assertInstanceOf('DOMDocument', $resources);
    }

    public function testGetAclResourcesMergedCorrectly()
    {
        $expectedFile = realpath(__DIR__) . '/../_files/acl_merged.xml';
        $expectedResources = new DOMDocument();
        $expectedResources->load($expectedFile);

        $actualResources = $this->_model->getAclResources();

        $this->assertNotEmpty($actualResources);
        $this->assertEqualXMLStructure($expectedResources->documentElement, $actualResources->documentElement, true);
        $this->assertEquals($expectedResources, $actualResources);
    }
}
