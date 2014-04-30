<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Category;

/**
 * @magentoAppArea adminhtml
 */
class TreeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Catalog\Block\Adminhtml\Category\Tree */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Block\Adminhtml\Category\Tree'
        );
    }

    public function testGetSuggestedCategoriesJson()
    {
        $this->assertEquals(
            '[{"id":"2","children":[],"is_active":"1","label":"Default Category"}]',
            $this->_block->getSuggestedCategoriesJson('Default')
        );
        $this->assertEquals('[]', $this->_block->getSuggestedCategoriesJson(strrev('Default')));
    }

    public function testGetLoadTreeUrl()
    {
        $this->assertContains('categoriesJson', $this->_block->getLoadTreeUrl());
    }
}
