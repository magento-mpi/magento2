<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Eav\Model\Entity\Attribute;

use \Magento\Eav\Api\Data\AttributeOptionLabelInterface;

class OptionLabelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Option
     */
    protected $model;

    protected function setUp()
    {
        $contextMock = $this->getMock('\Magento\Framework\Model\Context', [], [], '', false);
        $registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $metadataServiceMock = $this->getMock('\Magento\Framework\Service\Data\MetadataServiceInterface');
        $resourceCollectionMock = $this->getMock('\Magento\Framework\Data\Collection\Db', [], [], '', false);
        $resourceMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Model\Resource\AbstractResource',
            [],
            '',
            true,
            true,
            true,
            ['getIdFieldName']
        );

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            '\Magento\Eav\Model\Entity\Attribute\OptionLabel',
            [
                'context' => $contextMock,
                'registry' => $registryMock,
                'metadataService' => $metadataServiceMock,
                'resource' => $resourceMock,
                'resourceCollection' => $resourceCollectionMock,
                'data' => [
                    AttributeOptionLabelInterface::LABEL => 'labelMock',
                    AttributeOptionLabelInterface::STORE_ID => 'storeIdMock'

                ]
            ]
        );
    }

    public function testGetLabel()
    {
        $this->assertEquals('labelMock', $this->model->getLabel());
    }

    public function testGetStoreId()
    {
        $this->assertEquals('storeIdMock', $this->model->getStoreId());
    }
}
