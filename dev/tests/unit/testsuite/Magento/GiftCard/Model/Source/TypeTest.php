<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Source;

use Magento\TestFramework\Helper\ObjectManager;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Model\Source\Type
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_model = $objectManager->getObject('Magento\GiftCard\Model\Source\Type');
    }

    public function testGetFlatColumns()
    {
        $abstractAttributeMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            array('getAttributeCode', '__wakeup'),
            array(),
            '',
            false
        );
        $abstractAttributeMock->expects($this->any())->method('getAttributeCode')->will($this->returnValue('code'));

        $this->_model->setAttribute($abstractAttributeMock);

        $flatColumns = $this->_model->getFlatColumns();

        $this->assertTrue(is_array($flatColumns), 'FlatColumns must be an array value');
        $this->assertTrue(!empty($flatColumns), 'FlatColumns must be not empty');
        foreach ($flatColumns as $result) {
            $this->assertArrayHasKey('unsigned', $result, 'FlatColumns must have "unsigned" column');
            $this->assertArrayHasKey('default', $result, 'FlatColumns must have "default" column');
            $this->assertArrayHasKey('extra', $result, 'FlatColumns must have "extra" column');
            $this->assertArrayHasKey('type', $result, 'FlatColumns must have "type" column');
            $this->assertArrayHasKey('nullable', $result, 'FlatColumns must have "nullable" column');
            $this->assertArrayHasKey('comment', $result, 'FlatColumns must have "comment" column');
        }
    }

    /**
     * @param int $value
     * @param string $result
     * @dataProvider getOptionTextDataProvider
     */
    public function testGetOptionText($value, $result)
    {
        $this->assertEquals($result, $this->_model->getOptionText($value));
    }

    /**
     * @return array
     */
    public function getOptionTextDataProvider()
    {
        return [
            [0, 'Virtual'],
            [1, 'Physical'],
            [2, 'Combined'],
            [3, null]
        ];
    }

    public function testGetAllOptions()
    {
        $result = [
            [
                'value' => 0,
                'label' => 'Virtual'
            ],
            [
                'value' => 1,
                'label' => 'Physical'
            ],
            [
                'value' => 2,
                'label' => 'Combined'
            ]
        ];

        $this->assertEquals($result, $this->_model->getAllOptions());
    }
}
