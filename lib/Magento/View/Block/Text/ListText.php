<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Block\Text;

use Magento\View\Block\Text;

class ListText extends \Magento\View\Block\Text
{
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
