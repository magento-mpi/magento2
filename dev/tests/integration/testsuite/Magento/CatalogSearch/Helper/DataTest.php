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
namespace Magento\CatalogSearch\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\CatalogSearch\Helper\Data'
        );
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
        /** @var \Magento\TestFramework\ObjectManager  $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\CatalogSearch\Helper\Data $catalogSearchHelper */
        $catalogSearchHelper = $this->getMock(
            'Magento\CatalogSearch\Helper\Data',
            array('getQueryText'),
            array(
                $objectManager->get('Magento\Framework\App\Helper\Context'),
                $objectManager->get('Magento\Framework\Stdlib\String'),
                $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface'),
                $objectManager->get('Magento\CatalogSearch\Model\QueryFactory'),
                $objectManager->get('Magento\Escaper'),
                $objectManager->get('Magento\Filter\FilterManager')
            )
        );
        $catalogSearchHelper->expects(
            $this->any()
        )->method(
            'getQueryText'
        )->will(
            $this->returnValue('five <words> here <being> tested')
        );

        $catalogSearchHelper->checkNotes();

        $notes = implode($catalogSearchHelper->getNoteMessages());
        $this->assertContains('&lt;being&gt;', $notes);
        $this->assertNotContains('<being>', $notes);
    }
}
