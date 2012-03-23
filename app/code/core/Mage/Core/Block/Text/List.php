<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base html block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Core_Block_Text_List extends Mage_Core_Block_Text
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
