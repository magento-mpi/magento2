<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Filter_Template_Simple extends Magento_Object implements Zend_Filter_Interface
{
    protected $_startTag = '{{';
    protected $_endTag = '}}';

    public function setTags($start, $end)
    {
        $this->_startTag = $start;
        $this->_endTag = $end;
        return $this;
    }

    public function filter($value)
    {
        $callback = function ($matches) {
            return $this->getData($matches[1]);
        };
        $expression = '#' . preg_quote($this->_startTag, '#') . '(.*?)' . preg_quote($this->_endTag, '#') . '#';
        return preg_replace_callback($expression, $callback, $value);
    }
}
