<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Search_Model_Adapter_Solr_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Check Sku processing by getSearchEngineFieldName method with sort target
     */
    public function testGetSearchEngineFieldName()
    {
        $sku = new Magento_Object(array('attribute_code' => 'sku'));
        /** @var $model Magento_Search_Model_Adapter_Solr_Abstract */
        $model = $this->getMockForAbstractClass('Magento_Search_Model_Adapter_Solr_Abstract', array(), '', false);
        $this->assertEquals('sku', $model->getSearchEngineFieldName($sku, 'sku'));
        $this->assertEquals('attr_sort_sku', $model->getSearchEngineFieldName($sku, 'sort'));
    }
}
