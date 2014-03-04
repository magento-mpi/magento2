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
function unserialize($packed)
{
    return 'unpacked string';
}

class SerializedTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParametersWithoutDefinition()
    {
        $signatures = array();
        $definitions = array('wonderful' => null);
        $model = new \Magento\ObjectManager\Definition\Compiled\Serialized(array($signatures, $definitions));
        $this->assertEquals(null, $model->getParameters('wonderful'));
    }

    public function testGetParametersWithSignatureObject()
    {
        $wSignature = new \stdClass();
        $signatures = array('wonderfulClass' => $wSignature);
        $definitions = array('wonderful' => 'wonderfulClass');
        $model = new \Magento\ObjectManager\Definition\Compiled\Serialized(array($signatures, $definitions));
        $this->assertEquals($wSignature, $model->getParameters('wonderful'));
    }

    public function testGetParametersWithUnpacking()
    {
        $signatures = array('wonderfulClass' => 'packed code');
        $definitions = array('wonderful' => 'wonderfulClass');
        $model = new \Magento\ObjectManager\Definition\Compiled\Serialized(array($signatures, $definitions));
        $this->assertEquals('unpacked string', $model->getParameters('wonderful'));
    }
}