<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\App\Config\Scope;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Config\ScopePool|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopePool;

    /**
     * @var \Magento\App\Config\Scope\ResolverInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeResolver;

    /**
     * @var \Magento\App\Config\Loader
     */
    protected $_object;

    public function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_scopePool = $this->getMockBuilder('\Magento\App\Config\ScopePool')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_scopeResolver = $this->getMock('\Magento\App\Config\Scope\ResolverInterface');
        $this->_object = $helper->getObject('\Magento\App\Config\Loader', array(
            'scopePool' => $this->_scopePool,
            'scopeResolver' => $this->_scopeResolver,
        ));
    }

    public function testGetScopeCode()
    {
        $data = $this->getMock('Magento\App\Config\DataInterface');
        $this->_scopeResolver->expects($this->once())
            ->method('getScopeCode')
            ->will($this->returnValue('scopeCode'));
        $this->_scopePool->expects($this->once())
            ->method('getScopeByCode')
            ->with('scopeCode')
            ->will($this->returnValue($data));
        $this->assertInstanceOf('\Magento\App\Config\DataInterface', $this->_object->load());
    }
}
