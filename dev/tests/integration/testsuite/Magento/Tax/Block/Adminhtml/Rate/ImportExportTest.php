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
     * @var \Magento\Tax\Block\Adminhtml\Rate\ImportExport
     */
    protected $_block = null;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Tax\Block\Adminhtml\Rate\ImportExport');
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
