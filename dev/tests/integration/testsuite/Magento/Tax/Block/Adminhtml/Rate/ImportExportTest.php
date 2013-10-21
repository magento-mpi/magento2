<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Block\Adminhtml\Rate;

class ImportExportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Tax\Rate\ImportExport
     */
    protected $_block = null;

    protected function setUp()
    {
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Tax\Block\Adminhtml\Rate\ImportExport')
            ->setArea('adminhtml');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testCreateBlock()
    {
        $this->assertInstanceOf('Magento\Tax\Block\Adminhtml\Rate\ImportExport', $this->_block);
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
