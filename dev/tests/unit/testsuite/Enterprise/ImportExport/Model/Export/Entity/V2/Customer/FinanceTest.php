<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_ImportExport_Model_Export_Entity_V2_Eav_Customer_FinanceTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Test attribute code and website specific attribute code
     */
    const ATTRIBUTE_CODE         = 'code1';
    const WEBSITE_ATTRIBUTE_CODE = 'website1_code1';
    /**#@-*/

    /**
     * Websites array (website id => code)
     *
     * @var array
     */
    protected $_websites = array(
        Mage_Core_Model_App::ADMIN_STORE_ID => 'admin',
        1                                   => 'website1',
    );

    /**
     * Attributes array
     *
     * @var array
     */
    protected $_attributes = array(
        array(
            'attribute_id'   => 1,
            'attribute_code' => self::ATTRIBUTE_CODE,
        )
    );

    /**
     * Customer data
     *
     * @var array
     */
    protected $_customerData = array(
        'website_id'                 => 1,
        'email'                      => '@email@domain.com',
        self::WEBSITE_ATTRIBUTE_CODE => 1,
    );

    /**
     * Customer financial data export model
     *
     * @var Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance
     */
    protected $_model;

    public function setUp()
    {
        $this->_model
            = new Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance($this->_getModelDependencies());
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Create mocks for all $this->_model dependencies
     *
     * @return array
     */
    protected function _getModelDependencies()
    {
        $websiteManager = $this->getMock('stdClass', array('getWebsites'));
        $websiteManager->expects($this->exactly(2))
            ->method('getWebsites')
            ->will($this->returnCallback(array($this, 'getWebsites')));

        $translator = $this->getMock('stdClass', array('__'));
        $translator->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        /** @var $attributeCollection Varien_Data_Collection|PHPUnit_Framework_TestCase */
        $attributeCollection = $this->getMock('Varien_Data_Collection', array('getEntityTypeCode'));
        foreach ($this->_attributes as $attributeData) {
            $attribute = $this->getMockForAbstractClass('Mage_Eav_Model_Entity_Attribute_Abstract',
                array($attributeData), '', true, true, true, array('_construct')
            );
            $attributeCollection->addItem($attribute);
        }

        $data = array(
            'website_manager'              => $websiteManager,
            'store_manager'                => 'not_used',
            'translator'                   => $translator,
            'attribute_collection'         => $attributeCollection,
            'page_size'                    => 1,
            'collection_by_pages_iterator' => 'not_used',
            'entity_type_id'               => 1,
            'customer_collection'          => 'not_used',
            'customer_entity'              => 'not_used',
            'module_helper'                => 'not_used',
        );

        return $data;
    }

    /**
     * Get websites stub
     *
     * @param bool $withDefault
     * @return array
     */
    public function getWebsites($withDefault = false)
    {
        $websites = array();
        if (!$withDefault) {
            unset($websites[0]);
        }
        foreach ($this->_websites as $id => $code) {
            if (!$withDefault && $id == Mage_Core_Model_App::ADMIN_STORE_ID) {
                continue;
            }
            $websiteData = array(
                'id'   => $id,
                'code' => $code,
            );
            $websites[$id] = new Varien_Object($websiteData);
        }

        return $websites;
    }

    /**
     * Test for method exportItem()
     *
     * @covers Mage_ImportExport_Model_Export_Entity_V2_Eav_Customer::exportItem
     */
    public function testExportItem()
    {
        $writer = $this->getMockForAbstractClass('Mage_ImportExport_Model_Export_Adapter_Abstract',
            array(), '', false, false, true, array('writeRow')
        );

        $writer->expects($this->once())
            ->method('writeRow')
            ->will($this->returnCallback(array($this, 'validateWriteRow')));

        $this->_model->setWriter($writer);

        $item = $this->getMockForAbstractClass('Mage_Core_Model_Abstract',
            array($this->_customerData)
        );

        $this->_model->exportItem($item);
    }

    /**
     * Validate data passed to writer's writeRow() method
     *
     * @param array $row
     */
    public function validateWriteRow(array $row)
    {
        $emailColumn = Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance::COLUMN_EMAIL;
        $this->assertEquals($this->_customerData['email'], $row[$emailColumn]);

        $websiteColumn = Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance::COLUMN_WEBSITE;
        $this->assertEquals($this->_websites[$this->_customerData['website_id']], $row[$websiteColumn]);

        $financeWebsiteCol = Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance::COLUMN_FINANCE_WEBSITE;
        $this->assertEquals($this->_websites[$this->_customerData['website_id']], $row[$financeWebsiteCol]);

        $this->assertEquals($this->_customerData[self::WEBSITE_ATTRIBUTE_CODE], $row[self::ATTRIBUTE_CODE]);
    }
}
