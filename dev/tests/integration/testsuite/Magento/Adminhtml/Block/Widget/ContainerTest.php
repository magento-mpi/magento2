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

namespace Magento\Adminhtml\Block\Widget;

/**
 * @magentoAppArea adminhtml
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
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
     * @return \Magento\Adminhtml\Block\Widget\Container
     */
    protected function _buildBlock($titles)
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Mage::getModel(
            'Magento\Core\Model\Layout',
            array('area' => \Magento\Core\Model\App\Area::AREA_ADMINHTML)
        );
        /** @var $block \Magento\Adminhtml\Block\Widget\Container */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Widget\Container', 'block');
        foreach ($titles as $id => $title) {
            $block->addButton($id, array('title' => $title));
        }
        return $block;
    }
}
