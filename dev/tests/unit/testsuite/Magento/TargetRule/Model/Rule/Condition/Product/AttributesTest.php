<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Rule\Condition\Product;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class AttributesTest
 * @package Magento\TargetRule\Model\Rule\Condition\Product
 *
 *
 */
class AttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Attributes
     */
    protected $_attributes;

    public function setUp()
    {
        $productResource = $this->getMock('\Magento\Catalog\Model\Resource\Product',
            ['loadAllAttributes', 'loadValueOptions'],
            [],
            '',
            false
        );

        $productResource->expects($this->any())
            ->method('loadAllAttributes')
            ->will($this->returnSelf());

        $productResource->expects($this->any())
            ->method('loadValueOptions')
            ->will($this->returnSelf());

        $this->_attributes = (new ObjectManager($this))->getObject(
            '\Magento\TargetRule\Model\Rule\Condition\Product\Attributes',
            [
                'context' => $this->_getCleanMock('Magento\Rule\Model\Condition\Context'),
                'backendData' => $this->_getCleanMock('\Magento\Backend\Helper\Data'),
                'config' => $this->_getCleanMock('\Magento\Eav\Model\Config'),
                'productFactory' => $this->_getCleanMock('\Magento\Catalog\Model\ProductFactory'),
                'productResource' => $productResource,
                'attrSetCollection' => $this->_getCleanMock(
                        '\Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection'
                    ),
                'localeFormat' => $this->_getCleanMock('\Magento\Framework\Locale\FormatInterface'),
            ]
        );
    }

    /**
     * Get clean mock by class name
     *
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCleanMock($className)
    {
        return $this->getMock($className, [], [], '', false);
    }

    public function testGetNewChildSelectOptions()
    {
        $conditions = array(
            0 => array(
                'value' => 'Magento\TargetRule\Model\Rule\Condition\Product\Attributes|attribute_set_id',
                'label' => 'Attribute Set'
            ),
            1 => array(
                'value' => 'Magento\TargetRule\Model\Rule\Condition\Product\Attributes|category_ids',
                'label' => 'Category'
            )
        );
        $result = array('value' => $conditions, 'label' => __('Product Attributes'));

        $this->assertEquals($result, $this->_attributes->getNewChildSelectOptions());
    }
}
