<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\ObjectManager\Definition\Compiled;

class SerializedTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParametersWithoutDefinition()
    {
        $signatures = [];
        $definitions = ['wonderful' => null];
        $model = new \Magento\Framework\ObjectManager\Definition\Compiled\Serialized([$signatures, $definitions]);
        $this->assertEquals(null, $model->getParameters('wonderful'));
    }

    public function testGetParametersWithSignatureObject()
    {
        $wonderfulSignature = new \stdClass();
        $signatures = ['wonderfulClass' => $wonderfulSignature];
        $definitions = ['wonderful' => 'wonderfulClass'];
        $model = new \Magento\Framework\ObjectManager\Definition\Compiled\Serialized([$signatures, $definitions]);
        $this->assertEquals($wonderfulSignature, $model->getParameters('wonderful'));
    }

    public function testGetParametersWithUnpacking()
    {
        $checkString = 'code to pack';
        $signatures = ['wonderfulClass' => serialize($checkString)];
        $definitions = ['wonderful' => 'wonderfulClass'];
        $model = new \Magento\Framework\ObjectManager\Definition\Compiled\Serialized([$signatures, $definitions]);
        $this->assertEquals($checkString, $model->getParameters('wonderful'));
    }
}
