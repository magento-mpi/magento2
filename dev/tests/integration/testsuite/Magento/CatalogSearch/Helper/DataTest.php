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
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::getObjectManager()->get('Magento\CatalogSearch\Helper\Data');
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
        $this->assertInstanceOf('Magento\CatalogSearch\Helper\Data', $this->_helper->checkNotes());
    }

    /**
     * @magentoConfigFixture current_store catalog/search/search_type 1
     * @magentoConfigFixture current_store catalog/search/max_query_words 3
     */
    public function testCheckNotesEscapesHtmlWhenQueryIsCut()
    {
        /** @var $mock \Magento\CatalogSearch\Helper\Data */
        $mock = $this->getMock(
            'Magento\CatalogSearch\Helper\Data',
            array('getQueryText'), array(
                Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Helper\String'),
                Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Helper\Context'),
        ));
        $mock->expects($this->any())
            ->method('getQueryText')
            ->will($this->returnValue('five <words> here <being> tested'));

        $mock->checkNotes();

        $notes = implode($mock->getNoteMessages());
        $this->assertContains('&lt;being&gt;', $notes);
        $this->assertNotContains('<being>', $notes);
    }
}
