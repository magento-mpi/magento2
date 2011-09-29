<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Page
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Page_Block_Html_BreadcrumbsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Page_Block_Html_Breadcrumbs
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = new Mage_Page_Block_Html_Breadcrumbs();
    }

    public function testAddCrumb()
    {
        $this->assertEmpty($this->_block->toHtml());
        $info = array(
            'label' => 'test label',
            'title' => 'test title',
            'link'  => 'test link',
        );
        $this->_block->addCrumb('test', $info);
        $html = $this->_block->toHtml();
        $this->assertContains('test label', $html);
        $this->assertContains('test title', $html);
        $this->assertContains('test link', $html);
    }
}
