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

namespace Magento\Catalog\Model\Product\Attribute\Backend\Groupprice;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Groupprice\AbstractGroupprice
     */
    protected $_model;

    /**
     * Catalog helper
     *
     * @var \Magento\Catalog\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = $this->getMock('Magento\Catalog\Helper\Data', array('isPriceGlobal'), array(), '', false);
        $this->_helper->expects($this->any())
            ->method('isPriceGlobal')
            ->will($this->returnValue(true));

        $loggerMock = $this->getMock('Magento\Logger', array(), array(), '', false);
        $currencyFactoryMock = $this->getMock('Magento\Directory\Model\CurrencyFactory', array(), array(), '', false);
        $storeManagerMock = $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false);
        $productTypeMock = $this->getMock('Magento\Catalog\Model\Product\Type', array(), array(), '', false);
        $configMock = $this->getMock('Magento\App\ConfigInterface', array(), array(), '', false);

        $this->_model = $this->getMockForAbstractClass(
            'Magento\Catalog\Model\Product\Attribute\Backend\Groupprice\AbstractGroupprice',
            array(
                'logger' => $loggerMock,
                'currencyFactory' => $currencyFactoryMock,
                'storeManager' => $storeManagerMock,
                'catalogData' => $this->_helper,
                'config' => $configMock,
                'catalogProductType' => $productTypeMock,
            )
        );
        $resource = $this->getMock('StdClass', array('getMainTable'));
        $resource->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue('table'));

        $this->_model->expects($this->any())
            ->method('_getResource')
            ->will($this->returnValue($resource));
    }

    public function testGetAffectedFields()
    {
        $valueId = 10;
        $attributeId = 42;

        $attribute = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            array('getBackendTable', 'isStatic', 'getAttributeId', 'getName', '__wakeup'),
            array(),
            '',
            false
        );
        $attribute->expects($this->any())
            ->method('getAttributeId')
            ->will($this->returnValue($attributeId));

        $attribute->expects($this->any())
            ->method('isStatic')
            ->will($this->returnValue(false));

        $attribute->expects($this->any())
            ->method('getBackendTable')
            ->will($this->returnValue('table'));

        $attribute->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('group_price'));

        $this->_model->setAttribute($attribute);

        $object = new \Magento\Object();
        $object->setGroupPrice(array(array(
            'price_id' => 10
        )));
        $object->setId(555);

        $this->assertEquals(
            array(
                'table' => array(array(
                    'value_id' => $valueId,
                    'attribute_id' => $attributeId,
                    'entity_id' => $object->getId(),
                ))
            ),
            $this->_model->getAffectedFields($object)
        );
    }
}
