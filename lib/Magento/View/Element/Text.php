<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element;

/**
 * Class Text
 */
class Text extends \Magento\View\Element\AbstractBlock
{
    /**
     * Set text data
     *
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->setData('text', $text);
        return $this;
    }

    /**
     * Retrieve text data
     *
     * @return string
     */
    public function getText()
    {
        return $this->getData('text');
    }

    /**
     * Append text before|after existing text data
     *
     * @param string $text
     * @param bool $before
     * @return void
     */
    public function addText($text, $before = false)
    {
        if ($before) {
            $this->setText($text . $this->getText());
        } else {
            $this->setText($this->getText() . $text);
        }
    }

    /**
     * Render html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }
        return $this->getText();
    }
}
