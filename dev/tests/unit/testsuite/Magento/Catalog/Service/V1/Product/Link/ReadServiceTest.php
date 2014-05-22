<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

use Magento\Catalog\Service\V1\Product\Link\Data\LinkTypeEntity;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $providerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $builderMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->providerMock = $this->getMock('Magento\Catalog\Model\Product\LinkTypeProvider', [], [], '', false);
        $this->builderMock = $this->getMock(
            'Magento\Catalog\Service\V1\Product\Link\Data\LinkTypeEntityBuilder',
            [],
            [],
            '',
            false
        );
        $this->service = $helper->getObject(
            'Magento\Catalog\Service\V1\CatalogProductLinkService',
            [
                'linkTypeProvider' => $this->providerMock,
                'builder' => $this->builderMock,
            ]
        );
    }

    public function testGetProductLinkTypes()
    {
        $types = ['typeOne' => 'codeOne', 'typeTwo' => 'codeTwo'];

        $this->providerMock->expects($this->once())->method('getLinkTypes')->will($this->returnValue($types));

        $this->builderMock->expects($this->exactly(2))
            ->method('populateWithArray')
            ->with(
                $this->logicalOr(
                    $this->equalTo([LinkTypeEntity::TYPE => 'typeOne', LinkTypeEntity::CODE => 'codeOne']),
                    $this->equalTo([LinkTypeEntity::TYPE => 'typeTwo', LinkTypeEntity::CODE => 'codeTwo'])
                )
            )->will($this->returnSelf());

        $this->builderMock->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnSelf());

        $this->assertCount(2, $this->service->getProductLinkTypes());
    }

    public function testGetLinkedProducts()
    {
        $this->markTestIncomplete('need to be implemented');
    }
} 
