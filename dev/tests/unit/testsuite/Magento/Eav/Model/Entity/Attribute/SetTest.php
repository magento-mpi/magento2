<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Test class for \Magento\Eav\Model\Entity\Attribute\Set
 */
namespace Magento\Eav\Model\Entity\Attribute;

class SetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Set
     */
    protected $_model;

    protected function setUp()
    {
        $resource = $this->getMock('Magento\Eav\Model\Resource\Entity\Attribute\Set', [], [], '', false);
        $attrGroupFactory = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\GroupFactory',
            [],
            [],
            '',
            false,
            false
        );
        $attrFactory = $this->getMock('Magento\Eav\Model\Entity\AttributeFactory', [], [], '', false, false);
        $arguments = [
            'attrGroupFactory' => $attrGroupFactory,
            'attributeFactory' => $attrFactory,
            'resource' => $resource,
        ];
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento\Eav\Model\Entity\Attribute\Set', $arguments);
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @param string $attributeSetName
     * @param string $exceptionMessage
     * @dataProvider invalidAttributeSetDataProvider
     */
    public function testValidateWithExistingName($attributeSetName, $exceptionMessage)
    {
        $this->_model->getResource()->expects($this->any())->method('validate')->will($this->returnValue(false));

        $this->setExpectedException('Magento\Eav\Exception', $exceptionMessage);
        $this->_model->setAttributeSetName($attributeSetName);
        $this->_model->validate();
    }

    public function testValidateWithNonexistentValidName()
    {
        $this->_model->getResource()->expects($this->any())->method('validate')->will($this->returnValue(true));

        $this->_model->setAttributeSetName('nonexistent_name');
        $this->assertTrue($this->_model->validate());
    }

    /**
     * Retrieve data for invalid
     *
     * @return array
     */
    public function invalidAttributeSetDataProvider()
    {
        return [
            ['', 'Attribute set name is empty.'],
            ['existing_name', 'An attribute set with the "existing_name" name already exists.']
        ];
    }
}
