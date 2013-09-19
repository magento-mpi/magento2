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
     * @var \Magento\ImportExport\Model\Export\Entity\StubProduct
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
