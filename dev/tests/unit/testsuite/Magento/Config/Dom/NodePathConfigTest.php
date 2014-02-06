<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Config\Dom;

class NodePathConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodePathConfig
     */
    protected $_model;

    protected function setUp()
    {
        $map = array(
            '/root/node' => 'value 1',
            '/root/node/(sub-)?node' => 'value 2',
        );
        $this->_model = new NodePathConfig($map);
    }

    /**
     * @param string $xpath
     * @param mixed $expected
     *
     * @dataProvider getNodeInfoDataProvider
     */
    public function testGetNodeInfo($xpath, $expected)
    {
        $actual = $this->_model->getNodeInfo($xpath);
        $this->assertSame($expected, $actual);
    }

    public function getNodeInfoDataProvider()
    {
        return array(
            'nothing found' => array('/root', null),
            'simple path matched' => array('/root/node', 'value 1'),
            'regexp path matched' => array('/root/node/sub-node', 'value 2'),
            'path with attribute and namespace' => array('/mage:root/node[@name]', 'value 1'),
            'path matched partially' => array('/mega-root/root/node', null),
        );
    }
}
