<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Service\V1\Data\CatalogProductLink;

class CatalogProductLinkServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CatalogProductLinkService
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
        $this->providerMock = $this->getMock('Magento\Catalog\Model\Product\LinkTypeProvider', [], [], '', false);
        $this->builderMock = $this->getMock(
            'Magento\Catalog\Service\V1\Data\CatalogProductLinkBuilder',
            [],
            [],
            '',
            false
        );
        $this->service = new CatalogProductLinkService($this->providerMock, $this->builderMock);
    }

    public function testGetProductLinkTypes()
    {
        $types = ['typeOne' => 'codeOne', 'typeTwo' => 'codeTwo'];

        $this->providerMock->expects($this->once())->method('getLinkTypes')->will($this->returnValue($types));

        $this->builderMock->expects($this->exactly(2))
            ->method('populateWithArray')
            ->with(
                $this->logicalOr(
                    $this->equalTo([CatalogProductLink::TYPE => 'typeOne', CatalogProductLink::CODE => 'codeOne']),
                    $this->equalTo([CatalogProductLink::TYPE => 'typeTwo', CatalogProductLink::CODE => 'codeTwo'])
                )
            )->will($this->returnSelf());

        $this->builderMock->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnSelf());

        $this->assertCount(2, $this->service->getProductLinkTypes());
    }
} 
