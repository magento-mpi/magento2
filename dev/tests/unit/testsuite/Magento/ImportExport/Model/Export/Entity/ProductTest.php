<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Export\Entity;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Stub_UnitTest_\Magento\ImportExport\Model\Export\Entity\Product
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new StubProduct();
    }

    protected function tearDown()
    {
        unset($this->_object);
    }

    public function testUpdateDataWithCategoryColumnsNoCategoriesAssigned()
    {
        $dataRow = array();
        $productId = 1;
        $rowCategories = array($productId => array());

        $this->assertTrue($this->_object->updateDataWithCategoryColumns($dataRow, $rowCategories, $productId));
    }
}
