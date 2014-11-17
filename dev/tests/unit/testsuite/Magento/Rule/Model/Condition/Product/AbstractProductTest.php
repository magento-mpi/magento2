<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Rule\Model\Condition\Product;

class AbstractProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractProduct|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_condition;

    /**
     * @var \Magento\Framework\Object|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * @var \ReflectionProperty
     * 'Magento\Rule\Model\Condition\Product\AbstractProduct::_entityAttributeValues'
     */
    protected $_entityAttributeValuesProperty;

    /**
    * @var \ReflectionProperty
    * 'Magento\Rule\Model\Condition\Product\AbstractProduct::_config'
    */
    protected $_configProperty;

    public function setUp()
    {

        $this->_condition = $this->getMockForAbstractClass(
            '\Magento\Rule\Model\Condition\Product\AbstractProduct',
            [],
            '',
            false
        );
        $this->_entityAttributeValuesProperty = new \ReflectionProperty(
            'Magento\Rule\Model\Condition\Product\AbstractProduct',
            '_entityAttributeValues'
        );
        $this->_entityAttributeValuesProperty->setAccessible(true);

        $this->_configProperty = new \ReflectionProperty(
            'Magento\Rule\Model\Condition\Product\AbstractProduct',
            '_config'
        );
        $this->_configProperty->setAccessible(true);


    }

    public function testValidateAttributeEqualCategoryId()
    {
        $product = $this->getMock('\Magento\Framework\Object', array("getAttribute"), array(), '', false);
        $this->_condition->setAttribute('category_ids');
        $product->setAvailableInCategories(new \Magento\Framework\Object);
        $this->assertFalse($this->_condition->validate($product));
    }

    public function testValidateEmptyEntityAttributeValues()
    {
        $product = $this->getMock('\Magento\Framework\Object', array("getAttribute"), array(), '', false);
        $product->setId(1);
        $configProperty = new \ReflectionProperty(
            'Magento\Rule\Model\Condition\Product\AbstractProduct',
            '_entityAttributeValues'
        );
        $configProperty->setAccessible(true);
        $configProperty->setValue($this->_condition, array());
        $this->assertFalse($this->_condition->validate($product));
    }

    public function testValidateEmptyEntityAttributeValuesWithResource()
    {
        $product = $this->getMock('\Magento\Framework\Object', array("getAttribute"), array(), '', false);
        $product->setId(1);
        $time = '04/19/2012 11:59 am';
        $product->setData('someAttribute', $time);
        $this->_condition->setAttribute('someAttribute');
        $this->_entityAttributeValuesProperty->setValue($this->_condition, array());

        $this->_configProperty->setValue(
            $this->_condition,
            $this->getMock(
                'Magento\Eav\Model\Config',
                array(),
                array(),
                '',
                false
            )
        );

        $attribute = new \Magento\Framework\Object;
        $attribute->setBackendType('datetime');

        $newResource = $this->getMock('\Magento\Catalog\Model\Resource\Product', ['getAttribute'], [], '', false);
        $newResource->expects($this->any())
            ->method('getAttribute')
            ->with('someAttribute')
            ->will($this->returnValue($attribute));
        $newResource->_config = $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false);

        $product->setResource($newResource);
        $this->assertFalse($this->_condition->validate($product));

        $product->setData('someAttribute', 'option1,option2,option3');
        $attribute->setBackendType('null');
        $attribute->setFrontendInput('multiselect');

        $newResource = $this->getMock('\Magento\Catalog\Model\Resource\Product', ['getAttribute'], [], '', false);
        $newResource->expects($this->any())
            ->method('getAttribute')
            ->with('someAttribute')
            ->will($this->returnValue($attribute));
        $newResource->_config = $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false);

        $product->setResource($newResource);
        $this->assertFalse($this->_condition->validate($product));
    }

    public function testValidateSetEntityAttributeValuesWithResource()
    {
        $this->_condition->setAttribute('someAttribute');
        $product = $this->getMock('\Magento\Framework\Object', array('getAttribute'), array(), '', false);
        $product->setAtribute('attribute');
        $product->setId(12);

        $this->_configProperty->setValue(
            $this->_condition,
            $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false)
        );
        $this->_entityAttributeValuesProperty->setValue($this->_condition,
            $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false));

        $attribute = new \Magento\Framework\Object;
        $attribute->setBackendType('datetime');

        $newResource = $this->getMock('\Magento\Catalog\Model\Resource\Product', ['getAttribute'], [], '', false);
        $newResource->expects($this->any())
            ->method('getAttribute')
            ->with('someAttribute')
            ->will($this->returnValue($attribute));
        $newResource->_config = $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false);

        $product->setResource($newResource);

        $this->_entityAttributeValuesProperty->setValue(
            $this->_condition,
            array(
                1 => array('Dec. 1979 17:30'),
                2 => array('Dec. 1979 17:30'),
                3 => array('Dec. 1979 17:30')
            )
        );
        $this->assertFalse($this->_condition->validate($product));

    }

    public function testValidateSetEntityAttributeValuesWithoutResource()
    {
        $product = $this->getMock('\Magento\Framework\Object', array('someMethod'), array(), '', false);
        $this->_condition->setAttribute('someAttribute');
        $product->setAtribute('attribute');
        $product->setId(12);

        $this->_configProperty->setValue(
            $this->_condition,
            $this->getMock(
                'Magento\Eav\Model\Config',
                array(),
                array(),
                '',
                false
            )
        );

        $this->_entityAttributeValuesProperty->setValue(
            $this->_condition,
            $this->getMock(
                'Magento\Eav\Model\Config',
                array(),
                array(),
                '',
                false
            )
        );

        $attribute = new \Magento\Framework\Object;
        $attribute->setBackendType('multiselect');

        $newResource = $this->getMock('\Magento\Catalog\Model\Resource\Product', ['getAttribute'], [], '', false);
        $newResource->expects($this->any())
            ->method('getAttribute')
            ->with('someAttribute')
            ->will($this->returnValue($attribute));
        $newResource->_config = $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false);

        $product->setResource($newResource);

        $this->_entityAttributeValuesProperty->setValue(
            $this->_condition,
            array(
                1 => array(''),
                2 => array('option1,option2,option3'),
                3 => array('option1,option2,option3')
            )
        );

        $this->assertFalse($this->_condition->validate($product));

        $attribute = new \Magento\Framework\Object;
        $attribute->setBackendType(null);
        $attribute->setFrontendInput('multiselect');

        $newResource = $this->getMock('\Magento\Catalog\Model\Resource\Product', ['getAttribute'], [], '', false);
        $newResource->expects($this->any())
            ->method('getAttribute')
            ->with('someAttribute')
            ->will($this->returnValue($attribute));
        $newResource->_config = $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false);

        $product->setResource($newResource);
        $product->setId(1);
        $product->setData('someAttribute', 'value');

        $this->assertFalse($this->_condition->validate($product));
    }

    public function testGetjointTables()
    {
        $this->_condition->setAttribute('category_ids');
        $this->assertEquals([], $this->_condition->getTablesToJoin());
    }

    public function testGetMappedSqlField()
    {
        $this->_condition->setAttribute('category_ids');
        $this->assertEquals('e.entity_id', $this->_condition->getMappedSqlField());
    }
}
