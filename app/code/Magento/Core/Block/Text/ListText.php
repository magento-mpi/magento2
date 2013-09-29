<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base html block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Core\Block\Text;

class ListText extends \Magento\Core\Block\Text
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
