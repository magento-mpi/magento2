<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Adminhtml\Block\Catalog\Category;

class TreeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Adminhtml\Block\Catalog\Category\Tree */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = \Mage::getModel('Magento\Adminhtml\Block\Catalog\Category\Tree');
    }

    public function testGetSuggestedCategoriesJson()
    {
        $this->assertEquals(
            '[{"id":"2","children":[],"is_active":"1","label":"Default Category"}]',
            $this->_block->getSuggestedCategoriesJson('Default')
        );
        $this->assertEquals(
            '[]',
            $this->_block->getSuggestedCategoriesJson(strrev('Default'))
        );
    }
}
