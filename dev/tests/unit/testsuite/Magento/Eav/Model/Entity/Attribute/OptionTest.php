<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Eav\Model\Entity\Attribute;

use Magento\Eav\Api\Data\AttributeOptionInterface;

class OptionTest extends \PHPUnit_Framework_TestCase
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
            '\Magento\Eav\Model\Entity\Attribute\Option',
            [
                'context' => $contextMock,
                'registry' => $registryMock,
                'metadataService' => $metadataServiceMock,
                'resource' => $resourceMock,
                'resourceCollection' => $resourceCollectionMock,
                'data' => [
                    AttributeOptionInterface::LABEL => 'labelMock',
                    AttributeOptionInterface::VALUE => 'valueMock',
                    AttributeOptionInterface::SORT_ORDER => 'sortOrderMock',
                    AttributeOptionInterface::IS_DEFAULT => 'isDefaultMock',
                    AttributeOptionInterface::STORE_LABELS => 'storeLabelsMock'

                ]
            ]
        );
    }

    public function testGetLabel()
    {
        $this->assertEquals('labelMock', $this->model->getLabel());
    }

    public function testGetValue()
    {
        $this->assertEquals('valueMock', $this->model->getValue());
    }

    public function testGetSortOrder()
    {
        $this->assertEquals('sortOrderMock', $this->model->getSortOrder());
    }

    public function testGetIsDefault()
    {
        $this->assertEquals('isDefaultMock', $this->model->getIsDefault());
    }

    public function testGetStoreLabels()
    {
        $this->assertEquals('storeLabelsMock', $this->model->getStoreLabels());
    }
}
