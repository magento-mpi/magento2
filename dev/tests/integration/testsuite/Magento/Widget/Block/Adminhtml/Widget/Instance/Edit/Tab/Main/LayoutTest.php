<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main;

/**
 * @magentoAppArea adminhtml
 */
class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main\Layout
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main\Layout',
            '',
            array(
                'data' => array(
                    'widget_instance' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
                        'Magento\Widget\Model\Widget\Instance'
                    )
                )
            )
        );
        $this->_block->setLayout(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetLayoutsChooser()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\App\State'
        )->setAreaCode(
            \Magento\Core\Model\App\Area::AREA_FRONTEND
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\DesignInterface'
        )->setDefaultDesignTheme();

        $actualHtml = $this->_block->getLayoutsChooser();
        $this->assertStringStartsWith('<select ', $actualHtml);
        $this->assertStringEndsWith('</select>', $actualHtml);
        $this->assertContains('id="layout_handle"', $actualHtml);
        $optionCount = substr_count($actualHtml, '<option ');
        $this->assertGreaterThan(1, $optionCount, 'HTML select tag must provide options to choose from.');
        $this->assertEquals($optionCount, substr_count($actualHtml, '</option>'));
    }
}
