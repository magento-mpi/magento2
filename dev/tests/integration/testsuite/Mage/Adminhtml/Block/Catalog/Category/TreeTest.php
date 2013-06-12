<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Category_TreeTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Adminhtml_Block_Catalog_Category_Tree */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::getModel('Mage_Adminhtml_Block_Catalog_Category_Tree');
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
