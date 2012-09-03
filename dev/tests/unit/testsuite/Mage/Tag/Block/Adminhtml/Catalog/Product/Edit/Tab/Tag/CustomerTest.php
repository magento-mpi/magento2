<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_CustomerTest
    extends Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_TagTestCaseAbstract
{
    /**
     * @var string
     */
    protected $_modelName = 'Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer';

    /**
     * @var string
     */
    protected $_title = 'Customers Tagged Product';

    /**
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::getTabLabel
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::getTabTitle
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::canShowTab
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::isHidden
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::getTabClass
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::getAfter
     * @dataProvider methodListDataProvider
     * @param string $method
     */
    public function testDefinedPublicMethods($method)
    {
        $this->$method();
    }
}
