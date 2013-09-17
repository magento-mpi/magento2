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
class Magento_Core_Block_Text extends Magento_Core_Block_Abstract
{

    public function setText($text)
    {
        $this->setData('text', $text);
        return $this;
    }

    public function getText()
    {
        return $this->getData('text');
    }

    public function addText($text, $before=false)
    {
        if ($before) {
            $this->setText($text.$this->getText());
        } else {
            $this->setText($this->getText().$text);
        }
    }

    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        return $this->getText();
    }

}
