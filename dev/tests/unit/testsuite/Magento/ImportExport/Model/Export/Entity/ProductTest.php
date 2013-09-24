<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
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

/**
 * We had to create this stub class because _updateDataWithCategoryColumns() parameters are passed by reference -
 * we can't use ReflectionMethod::setAccessible() and then ReflectionMethod::invokeArgs() to call it from test.
 */
class Stub_UnitTest_Magento_ImportExport_Model_Export_Entity_Product
    extends \Magento\ImportExport\Model\Export\Entity\Product
{
    /**
     * Disable parent constructor
     */
    public function __construct()
    {
    }

    /**
     * Update data row with information about categories. Return true, if data row was updated
     *
     * @param array $dataRow
     * @param array $rowCategories
     * @param int $productId
     * @return bool
     */
    public function updateDataWithCategoryColumns(&$dataRow, &$rowCategories, $productId)
    {
        return $this->_updateDataWithCategoryColumns($dataRow, $rowCategories, $productId);
    }
}
