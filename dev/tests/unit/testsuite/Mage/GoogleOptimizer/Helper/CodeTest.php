<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Helper_CodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_codeModelMock;

    /**
     * @var Mage_GoogleOptimizer_Helper_Code
     */
    protected $_helper;

    public function setUp()
    {
        $this->_codeModelMock = $this->getMock('Mage_GoogleOptimizer_Model_Code', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_helper = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Helper_Code', array(
            'code' => $this->_codeModelMock,
        ));
    }

    public function testLoadingCodeForCategoryEntity()
    {
        $categoryMock = $this->getMock('Magento_Catalog_Model_Category', array(), array(), '', false);

        $categoryId = 1;
        $storeId = 1;

        $categoryMock->expects($this->exactly(2))->method('getId')->will($this->returnValue($categoryId));
        $categoryMock->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));
        $this->_codeModelMock->expects($this->once())->method('loadByEntityIdAndType')->with(
            $categoryId,
            Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_CATEGORY,
            $storeId
        );

        $this->assertEquals($this->_codeModelMock, $this->_helper->getCodeObjectByEntity($categoryMock,
            Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_CATEGORY));
    }

    public function testLoadingCodeForProductEntity()
    {
        $productMock = $this->getMock('Magento_Catalog_Model_Product', array(), array(), '', false);

        $categoryId = 1;
        $storeId = 1;

        $productMock->expects($this->exactly(2))->method('getId')->will($this->returnValue($categoryId));
        $productMock->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));
        $this->_codeModelMock->expects($this->once())->method('loadByEntityIdAndType')->with(
            $categoryId,
            Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
            $storeId
        );

        $this->assertEquals($this->_codeModelMock, $this->_helper->getCodeObjectByEntity($productMock,
            Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT));
    }

    public function testLoadingCodeForPageEntity()
    {
        $pageMock = $this->getMock('Mage_Cms_Model_Page', array(), array(), '', false);

        $categoryId = 1;

        $pageMock->expects($this->exactly(2))->method('getId')->will($this->returnValue($categoryId));
        $this->_codeModelMock->expects($this->once())->method('loadByEntityIdAndType')->with(
            $categoryId,
            Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE
        );

        $this->assertEquals($this->_codeModelMock, $this->_helper->getCodeObjectByEntity($pageMock,
            Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The model class is not valid
     */
    public function testExceptionNotValidEntityType()
    {
        $entity = $this->getMock('Mage_Cms_Model_Block', array(), array(), '', false);

        $entityId = 1;

        $entity->expects($this->exactly(2))->method('getId')->will($this->returnValue($entityId));
        $this->_codeModelMock->expects($this->never())->method('loadByEntityIdAndType');

        $this->assertEquals($this->_codeModelMock, $this->_helper->getCodeObjectByEntity($entity,
            Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The model is empty
     */
    public function testExceptionEmptyEntity()
    {
        $entity = $this->getMock('Mage_Cms_Model_Block', array(), array(), '', false);

        $entityId = 0;

        $entity->expects($this->exactly(1))->method('getId')->will($this->returnValue($entityId));
        $this->_codeModelMock->expects($this->never())->method('loadByEntityIdAndType');

        $this->assertEquals($this->_codeModelMock, $this->_helper->getCodeObjectByEntity($entity,
            Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE));
    }
}
