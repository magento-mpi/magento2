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

class Magento_Core_Block_Text_List extends Magento_Core_Block_Text
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
