<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CatalogEvent_Model_EventTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_CatalogEvent_Model_Event
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_CatalogEvent_Model_Event');
    }

    protected function _getDate($time = 'now')
    {
        return date('Y-m-d H:i:s', strtotime($time));
    }

    public function testCRUD()
    {
        $this->_model
            ->setCategoryId(1)
            ->setDateStart($this->_getDate('-1 day'))
            ->setDateEnd($this->_getDate('+1 day'))
            ->setDisplayState(Magento_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE)
            ->setSortOrder(null)
        ;
        $crud = new Magento_TestFramework_Entity($this->_model, array(
            'category_id'   => 2,
            'date_start'    => $this->_getDate('-1 year'),
            'date_end'      => $this->_getDate('+1 month'),
            'display_state' => Magento_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE,
            'sort_order'    => 123,
        ));
        $crud->testCrud();
    }
}
