<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Definition\Compiled;

class BinaryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParametersWithUnpacking()
    {
        $checkString = 'packed code';
        $signatures = array('wonderfulClass' => igbinary_serialize($checkString));
        $definitions = array('wonderful' => 'wonderfulClass');
        $model = new \Magento\ObjectManager\Definition\Compiled\Binary(array($signatures, $definitions));
        $this->assertEquals($checkString, $model->getParameters('wonderful'));
    }
}