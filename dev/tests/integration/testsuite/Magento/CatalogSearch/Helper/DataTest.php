<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        /** @var \Magento\TestFramework\ObjectManager  $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');
        $request->setParam('q', 'five <words> here <being> tested');
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
}
