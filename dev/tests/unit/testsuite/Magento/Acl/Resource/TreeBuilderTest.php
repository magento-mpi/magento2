<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Resource_TreeBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Acl_Resource_TreeBuilder
     */
    protected $_model;

    /**
     * Path to fixture
     *
     * @var string
     */
    protected $_fixturePath;

    protected function setUp()
    {
        $this->_model = new Magento_Acl_Resource_TreeBuilder();
        $this->_fixturePath = realpath(__DIR__ . '/../../')
            . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'Acl'
            . DIRECTORY_SEPARATOR . 'Resource'
            . DIRECTORY_SEPARATOR;
    }

    public function testBuild()
    {
        $resourceList = require ($this->_fixturePath . 'resourceList.php');
        $actual = require($this->_fixturePath . 'result.php');
        $expected = $this->_model->build($resourceList);
        $this->assertEquals($actual, $expected);
    }
}