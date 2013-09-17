<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Helper_CodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_codeModelMock;

    /**
     * @var Magento_GoogleOptimizer_Helper_Code
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_codeModelMock = $this->getMock('Magento_GoogleOptimizer_Model_Code', array(), array(), '', false);

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_helper = $objectManagerHelper->getObject('Magento_GoogleOptimizer_Helper_Code', array(
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
            Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_CATEGORY,
            $storeId
        );

        $this->assertEquals($this->_codeModelMock, $this->_helper->getCodeObjectByEntity($categoryMock,
            Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_CATEGORY));
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
            Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
            $storeId
        );

        $this->assertEquals($this->_codeModelMock, $this->_helper->getCodeObjectByEntity($productMock,
            Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT));
    }

    public function testLoadingCodeForPageEntity()
    {
        $pageMock = $this->getMock('Magento_Cms_Model_Page', array(), array(), '', false);

        $categoryId = 1;

        $pageMock->expects($this->exactly(2))->method('getId')->will($this->returnValue($categoryId));
        $this->_codeModelMock->expects($this->once())->method('loadByEntityIdAndType')->with(
            $categoryId,
            Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE
        );

        $this->assertEquals($this->_codeModelMock, $this->_helper->getCodeObjectByEntity($pageMock,
            Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The model class is not valid
     */
    public function testExceptionNotValidEntityType()
    {
        $entity = $this->getMock('Magento_Cms_Model_Block', array(), array(), '', false);

        $entityId = 1;

        $entity->expects($this->exactly(2))->method('getId')->will($this->returnValue($entityId));
        $this->_codeModelMock->expects($this->never())->method('loadByEntityIdAndType');

        $this->assertEquals($this->_codeModelMock, $this->_helper->getCodeObjectByEntity($entity,
            Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The model is empty
     */
    public function testExceptionEmptyEntity()
    {
        $entity = $this->getMock('Magento_Cms_Model_Block', array(), array(), '', false);

        $entityId = 0;

        $entity->expects($this->exactly(1))->method('getId')->will($this->returnValue($entityId));
        $this->_codeModelMock->expects($this->never())->method('loadByEntityIdAndType');

        $this->assertEquals($this->_codeModelMock, $this->_helper->getCodeObjectByEntity($entity,
            Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE));
    }
}
