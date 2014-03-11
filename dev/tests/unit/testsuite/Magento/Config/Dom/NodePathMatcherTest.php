<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Config\Dom;

class NodePathMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodePathMatcher
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new NodePathMatcher();
    }

    /**
     * @param string $pathPattern
     * @param string $xpathSubject
     * @param boolean $expectedResult
     *
     * @dataProvider getNodeInfoDataProvider
     */
    public function testMatch($pathPattern, $xpathSubject, $expectedResult)
    {
        $actualResult = $this->_model->match($pathPattern, $xpathSubject);
        $this->assertSame($expectedResult, $actualResult);
    }

    public function getNodeInfoDataProvider()
    {
        return array(
            'no match'              => array('/root/node', '/root', false),
            'partial match'         => array('/root/node', '/wrapper/root/node', false),
            'exact match'           => array('/root/node', '/root/node', true),
            'regexp match'          => array('/root/node/(sub-)+node', '/root/node/sub-node', true),
            'match with namespace'  => array('/root/node', '/mage:root/node', true),
            'match with predicate'  => array('/root/node', '/root/node[@name="test"]', true),
        );
    }
}
