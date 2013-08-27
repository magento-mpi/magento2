<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CatalogSearch_Block_TermTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_CatalogSearch_Block_Term
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_CatalogSearch_Block_Term');
    }

    public function testGetSearchUrl()
    {
        $query = uniqid();
        $obj = new Magento_Object(array('name' => $query));
        $this->assertStringEndsWith("/catalogsearch/result/?q={$query}", $this->_block->getSearchUrl($obj));
    }
}
