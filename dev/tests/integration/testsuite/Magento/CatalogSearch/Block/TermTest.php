<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Block;

class TermTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogSearch\Block\Term
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\CatalogSearch\Block\Term'
        );
    }

    public function testGetSearchUrl()
    {
        $query = uniqid();
        $obj = new \Magento\Framework\Object(array('name' => $query));
        $this->assertStringEndsWith("/catalogsearch/result/?q={$query}", $this->_block->getSearchUrl($obj));
    }
}
