<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Config\Resolver;

class ByPatternTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $path = 'app/code/';
        $pattern = '*/*/module.xml';
        $globResult = array('/home/user/webroot/app/code/Magento/Core/module.xml');

        $glob = $this->getMockBuilder('Magento\Config\GlobWrapper')->getMock();
        $glob->expects($this->once())
            ->method('glob')
            ->with($path . $pattern)
            ->will($this->returnValue($globResult));

        /** @var \Magento\Config\GlobWrapper $glob */
        $resolver = new ByPattern($glob, $path, $pattern);
        $this->assertEquals($globResult, $resolver->get());
    }
}
