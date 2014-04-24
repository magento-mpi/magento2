<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\ObjectManager\Definition\Compiled;

class SerializedTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParametersWithoutDefinition()
    {
        $signatures = array();
        $definitions = array('wonderful' => null);
        $model = new \Magento\Framework\ObjectManager\Definition\Compiled\Serialized(array($signatures, $definitions));
        $this->assertEquals(null, $model->getParameters('wonderful'));
    }

    public function testGetParametersWithSignatureObject()
    {
        $wonderfulSignature = new \stdClass();
        $signatures = array('wonderfulClass' => $wonderfulSignature);
        $definitions = array('wonderful' => 'wonderfulClass');
        $model = new \Magento\Framework\ObjectManager\Definition\Compiled\Serialized(array($signatures, $definitions));
        $this->assertEquals($wonderfulSignature, $model->getParameters('wonderful'));
    }

    public function testGetParametersWithUnpacking()
    {
        $checkString = 'code to pack';
        $signatures = array('wonderfulClass' => serialize($checkString));
        $definitions = array('wonderful' => 'wonderfulClass');
        $model = new \Magento\Framework\ObjectManager\Definition\Compiled\Serialized(array($signatures, $definitions));
        $this->assertEquals($checkString, $model->getParameters('wonderful'));
    }
}