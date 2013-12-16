<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Acl\Resource;

class TreeBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Acl\Resource\TreeBuilder
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
        $this->_model = new \Magento\Acl\Resource\TreeBuilder();
        $this->_fixturePath = realpath(__DIR__ . '/../../') . '/_files/Acl/Resource/';
    }

    public function testBuild()
    {
        $resourceList = require ($this->_fixturePath . 'resourceList.php');
        $actual = require($this->_fixturePath . 'result.php');
        $expected = $this->_model->build($resourceList);
        $this->assertEquals($actual, $expected);
    }
}
