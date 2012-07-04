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

class Mage_CatalogSearch_Block_TermTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_CatalogSearch_Block_Term
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = new Mage_CatalogSearch_Block_Term;
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testGetSearchUrl()
    {
        $query = uniqid();
        $obj = new Varien_Object(array('name' => $query));
        $this->assertStringEndsWith("/catalogsearch/result/?q={$query}", $this->_block->getSearchUrl($obj));
    }
}
