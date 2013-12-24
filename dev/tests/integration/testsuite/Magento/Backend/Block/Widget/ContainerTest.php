<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Block\Widget;

/**
 * @magentoAppArea adminhtml
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testPseudoConstruct()
    {
        /** @var $block \Magento\Backend\Block\Widget\Container */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Backend\Block\Widget\Container', '',
                array('data' => array(
                    \Magento\Backend\Block\Widget\Container::PARAM_CONTROLLER => 'one',
                    \Magento\Backend\Block\Widget\Container::PARAM_HEADER_TEXT => 'two',
                ))
            );
        $this->assertStringEndsWith('one', $block->getHeaderCssClass());
        $this->assertContains('two', $block->getHeaderText());
    }

    public function testGetButtonsHtml()
    {
        $titles = array(1 => 'Title 1', 'Title 2', 'Title 3');
        $block = $this->_buildBlock($titles);
        $html = $block->getButtonsHtml();

        $this->assertContains('<button', $html);
        foreach ($titles as $title) {
            $this->assertContains($title, $html);
        }
    }

    public function testUpdateButton()
    {
        $originalTitles = array(1 => 'Title 1', 'Title 2', 'Title 3');
        $newTitles = array(1 => 'Button A', 'Button B', 'Button C');

        $block = $this->_buildBlock($originalTitles);
        $html = $block->getButtonsHtml();
        foreach ($newTitles as $newTitle) {
            $this->assertNotContains($newTitle, $html);
        }

        $block = $this->_buildBlock($originalTitles); // Layout caches html, thus recreate block for further testing
        foreach ($newTitles as $id => $newTitle) {
            $block->updateButton($id, 'title', $newTitle);
        }
        $html = $block->getButtonsHtml();
        foreach ($newTitles as $newTitle) {
            $this->assertContains($newTitle, $html);
        }
    }

    /**
     * Composes a container with several buttons in it
     *
     * @param array $titles
     * @return \Magento\Backend\Block\Widget\Container
     */
    protected function _buildBlock($titles)
    {
        /** @var $layout \Magento\View\LayoutInterface */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Layout',
            array('area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
        );
        /** @var $block \Magento\Backend\Block\Widget\Container */
        $block = $layout->createBlock('Magento\Backend\Block\Widget\Container', 'block');
        foreach ($titles as $id => $title) {
            $block->addButton($id, array('title' => $title));
        }
        return $block;
    }
}
