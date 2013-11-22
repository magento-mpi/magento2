<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Block\Text;

use Magento\View\Block\Text;

/**
 * Class ListText
 */
class ListText extends \Magento\View\Block\Text
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
            $this->addText($layout->renderElement($child));
        }

        return parent::_toHtml();
    }
}
