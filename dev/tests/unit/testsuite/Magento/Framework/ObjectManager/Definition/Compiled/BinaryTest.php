<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\ObjectManager\Definition\Compiled;

class BinaryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParametersWithUnpacking()
    {
        if (!function_exists('igbinary_serialize')) {
            $this->markTestSkipped('This test requires igbinary PHP extension');
        }
        $checkString = 'packed code';
        $signatures = ['wonderfulClass' => igbinary_serialize($checkString)];
        $definitions = ['wonderful' => 'wonderfulClass'];
        $model = new \Magento\Framework\ObjectManager\Definition\Compiled\Binary([$signatures, $definitions]);
        $this->assertEquals($checkString, $model->getParameters('wonderful'));
    }
}
