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

class Magento_CatalogSearch_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_CatalogSearch_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::helper('Magento_CatalogSearch_Helper_Data');
    }

    public function testGetResultUrl()
    {
        $this->assertStringEndsWith('/catalogsearch/result/', $this->_helper->getResultUrl());

        $query = uniqid();
        $this->assertStringEndsWith("/catalogsearch/result/?q={$query}", $this->_helper->getResultUrl($query));
    }

    public function testGetAdvancedSearchUrl()
    {
        $this->assertStringEndsWith('/catalogsearch/advanced/', $this->_helper->getAdvancedSearchUrl());
    }

    public function testCheckNotesResult()
    {
        $this->assertInstanceOf('Magento_CatalogSearch_Helper_Data', $this->_helper->checkNotes());
    }

    /**
     * @magentoConfigFixture current_store catalog/search/search_type 1
     * @magentoConfigFixture current_store catalog/search/max_query_words 3
     */
    public function testCheckNotesEscapesHtmlWhenQueryIsCut()
    {
        /** @var $mock Magento_CatalogSearch_Helper_Data */
        $mock = $this->getMock(
            'Magento_CatalogSearch_Helper_Data',
            array('getQueryText'), array(Mage::getObjectManager()->get('Magento_Core_Helper_Context'))
        );
        $mock->expects($this->any())
            ->method('getQueryText')
            ->will($this->returnValue('five <words> here <being> tested'));

        $mock->checkNotes();

        $notes = implode($mock->getNoteMessages());
        $this->assertContains('&lt;being&gt;', $notes);
        $this->assertNotContains('<being>', $notes);
    }
}
