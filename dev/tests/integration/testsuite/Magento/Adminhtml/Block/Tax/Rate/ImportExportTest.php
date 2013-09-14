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
class Magento_Adminhtml_Block_Tax_Rate_ImportExportTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Catalog\Product\Attribute\Edit\Tab\Main
     */
    protected $_block = null;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Tax\Rate\ImportExport')
            ->setArea('adminhtml');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testCreateBlock()
    {
        $this->assertInstanceOf('Magento\Adminhtml\Block\Tax\Rate\ImportExport', $this->_block);
    }

    public function testFormExists()
    {
        $html = $this->_block->toHtml();

        $this->assertContains(
            '<form id="import-form"', $html
        );

        $this->assertContains(
            '<form id="export_form"', $html
        );
    }
}
