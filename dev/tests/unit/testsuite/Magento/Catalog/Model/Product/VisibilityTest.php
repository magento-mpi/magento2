<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

use Magento\TestFramework\Helper\ObjectManager;

class VisibilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_model = $objectManager->getObject('Magento\Catalog\Model\Product\Visibility');
    }

    public function testGetFlatColums()
    {
        $abstractAttributeMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            array('getAttributeCode', '__wakeup'), array(), '', false
        );

        $abstractAttributeMock->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue('code'));

        $this->_model->setAttribute($abstractAttributeMock);

        $flatColums = $this->_model->getFlatColums();

        $this->assertTrue(is_array($flatColums), 'FlatColums must be an array value');
        $this->assertTrue(!empty($flatColums), 'FlatColums must be not empty');
        foreach ($flatColums as $result) {
            $this->assertArrayHasKey('unsigned', $result, 'FlatColums must have "unsigned" column');
            $this->assertArrayHasKey('default', $result, 'FlatColums must have "default" column');
            $this->assertArrayHasKey('extra', $result, 'FlatColums must have "extra" column');
            $this->assertArrayHasKey('type', $result, 'FlatColums must have "type" column');
            $this->assertArrayHasKey('nullable', $result, 'FlatColums must have "nullable" column');
            $this->assertArrayHasKey('comment', $result, 'FlatColums must have "comment" column');
        }
    }
}
