<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Layout;

use Magento\Framework\View\Element\Template;

/**
 * Class Tabs
 */
class Tabs extends AbstractStructure
{
    /**
     * Get tabs
     *
     * @return array
     */
    public function getTabs()
    {
        $tabs = $this->renderContext->getStorage()->getLayoutNode('sections');
        return isset($tabs['children']) ? $tabs['children'] : [];
    }
}
