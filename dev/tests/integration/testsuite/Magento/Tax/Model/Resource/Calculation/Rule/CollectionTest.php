<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Resource\Calculation\Rule;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * Test setClassTypeFilter with correct Class Type
     *
     * @param $classType
     * @param $elementId
     * @param $expected
     *
     * @dataProvider setClassTypeFilterDataProvider
     */
    public function testSetClassTypeFilter($classType, $elementId, $expected)
    {
        $collection = $this->_objectManager->create('Magento\Tax\Model\Resource\Calculation\Rule\Collection');
        $collection->setClassTypeFilter($classType, $elementId);
        $this->assertRegExp($expected, (string)$collection->getSelect());
    }

    public function setClassTypeFilterDataProvider()
    {
        return array(
            array(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT, 1,
                '/`?cd`?\.`?product_tax_class_id`? = [\S]{0,1}1[\S]{0,1}/'),
            array(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER, 1,
                '/`?cd`?\.`?customer_tax_class_id`? = [\S]{0,1}1[\S]{0,1}/')
        );
    }

    /**
     * Test setClassTypeFilter with wrong Class Type
     *
     * @expectedException \Magento\Model\Exception
     */
    public function testSetClassTypeFilterWithWrongType()
    {
        $collection = $this->_objectManager->create('Magento\Tax\Model\Resource\Calculation\Rule\Collection');
        $collection->setClassTypeFilter('WrongType', 1);
    }
}
