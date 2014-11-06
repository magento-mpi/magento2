<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

class LinkTypeResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LinkTypeResolver
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $providerMock;

    protected function setUp()
    {
        $this->providerMock = $this->getMock('Magento\Catalog\Model\Product\LinkTypeProvider', [], [], '', false);
        $this->model = new LinkTypeResolver($this->providerMock);
    }

    public function testGetTypeIdByCode()
    {
        $linkTypes = ['crosssell' => 1, 'upsell' => 2, 'related' => 4];
        $this->providerMock->expects($this->once())->method('getLinkTypes')->will($this->returnValue($linkTypes));
        $this->assertEquals(4, $this->model->getTypeIdByCode('related'));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Unknown link type code is provided
     */
    public function testGetTypeIdByCodeWithInvalidType()
    {
        $linkTypes = ['crosssell' => 1, 'upsell' => 2, 'related' => 4];
        $this->providerMock->expects($this->once())->method('getLinkTypes')->will($this->returnValue($linkTypes));
        $this->model->getTypeIdByCode('invalid_type');
    }
}
