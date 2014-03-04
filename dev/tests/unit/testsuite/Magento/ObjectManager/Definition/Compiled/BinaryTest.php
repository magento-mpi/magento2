<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Definition\Compiled;

/**
 * @param string $packed
 * @return string
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function igbinary_unserialize($packed)
{
    return 'unpacked string';
}

class BinaryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParametersWithUnpacking()
    {
        $signatures = array('wonderfulClass' => 'packed code');
        $definitions = array('wonderful' => 'wonderfulClass');
        $model = new \Magento\ObjectManager\Definition\Compiled\Binary(array($signatures, $definitions));
        $this->assertEquals('unpacked string', $model->getParameters('wonderful'));
    }
}