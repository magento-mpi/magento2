<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config\Dom;

class ArrayNodeConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayNodeConfig
     */
    protected $object;

    /**
     * @var NodePathMatcher|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $nodePathMatcher;

    protected function setUp()
    {
        $this->nodePathMatcher = $this->getMock('\Magento\Config\Dom\NodePathMatcher');
        $this->object = new ArrayNodeConfig(
            $this->nodePathMatcher,
            array('/root/assoc/one' => 'name', '/root/assoc/two' => 'id', '/root/assoc/three' => 'key'),
            array('/root/numeric/one', '/root/numeric/two', '/root/numeric/three')
        );
    }

    public function testIsNumericArrayMatched()
    {
        $xpath = '/root/numeric[@attr="value"]/two';
        $this->nodePathMatcher->expects(
            $this->at(0)
        )->method(
            'match'
        )->with(
            '/root/numeric/one',
            $xpath
        )->will(
            $this->returnValue(false)
        );
        $this->nodePathMatcher->expects(
            $this->at(1)
        )->method(
            'match'
        )->with(
            '/root/numeric/two',
            $xpath
        )->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->object->isNumericArray($xpath));
    }

    public function testIsNumericArrayNotMatched()
    {
        $xpath = '/root/numeric[@attr="value"]/four';
        $this->nodePathMatcher->expects(
            $this->at(0)
        )->method(
            'match'
        )->with(
            '/root/numeric/one',
            $xpath
        )->will(
            $this->returnValue(false)
        );
        $this->nodePathMatcher->expects(
            $this->at(1)
        )->method(
            'match'
        )->with(
            '/root/numeric/two',
            $xpath
        )->will(
            $this->returnValue(false)
        );
        $this->nodePathMatcher->expects(
            $this->at(2)
        )->method(
            'match'
        )->with(
            '/root/numeric/three',
            $xpath
        )->will(
            $this->returnValue(false)
        );
        $this->assertFalse($this->object->isNumericArray($xpath));
    }

    public function testGetAssocArrayKeyAttributeMatched()
    {
        $xpath = '/root/assoc[@attr="value"]/two';
        $this->nodePathMatcher->expects(
            $this->at(0)
        )->method(
            'match'
        )->with(
            '/root/assoc/one',
            $xpath
        )->will(
            $this->returnValue(false)
        );
        $this->nodePathMatcher->expects(
            $this->at(1)
        )->method(
            'match'
        )->with(
            '/root/assoc/two',
            $xpath
        )->will(
            $this->returnValue(true)
        );
        $this->assertEquals('id', $this->object->getAssocArrayKeyAttribute($xpath));
    }

    public function testGetAssocArrayKeyAttributeNotMatched()
    {
        $xpath = '/root/assoc[@attr="value"]/four';
        $this->nodePathMatcher->expects(
            $this->at(0)
        )->method(
            'match'
        )->with(
            '/root/assoc/one',
            $xpath
        )->will(
            $this->returnValue(false)
        );
        $this->nodePathMatcher->expects(
            $this->at(1)
        )->method(
            'match'
        )->with(
            '/root/assoc/two',
            $xpath
        )->will(
            $this->returnValue(false)
        );
        $this->nodePathMatcher->expects(
            $this->at(2)
        )->method(
            'match'
        )->with(
            '/root/assoc/three',
            $xpath
        )->will(
            $this->returnValue(false)
        );
        $this->assertNull($this->object->getAssocArrayKeyAttribute($xpath));
    }
}
