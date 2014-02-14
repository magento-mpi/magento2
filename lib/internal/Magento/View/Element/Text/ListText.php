<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element\Text;

use Magento\View\Element\Text;

/**
 * Class ListText
 */
class ListText extends \Magento\View\Element\Text
{
    /**
     * Render html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setText('');

        $layout = $this->getLayout();
        foreach ($this->getChildNames() as $child) {
            $this->addText($layout->renderElement($child, false));
        }

        return parent::_toHtml();
    }
}
