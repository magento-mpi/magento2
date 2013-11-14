<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * HTML anchor element block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\View\Block\Html;

class Link extends \Magento\View\Block\Template
{
    /**
     * Prepare link attributes as serialized and formated string
     *
     * @return string
     */
    public function getLinkAttributes()
    {
        $allow = array(
            'href', 'title', 'charset', 'name', 'hreflang', 'rel', 'rev', 'accesskey', 'shape',
            'coords', 'tabindex', 'onfocus', 'onblur', // %attrs
            'id', 'class', 'style', // %coreattrs
            'lang', 'dir', // %i18n
            'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove',
            'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup' // %events
        );

        $attributes = array();
        foreach ($allow as $attribute) {
            $value = $this->getDataUsingMethod($attribute);
            if (!is_null($value)) {
                $attributes[$attribute] = $this->escapeHtml($value);
            }
        }

        if (!empty($attributes)) {
            return $this->serialize($attributes);
        }
        return '';
    }

    /**
     * serialize attributes
     *
     * @param   array $attributes
     * @param   string $valueSeparator
     * @param   string $fieldSeparator
     * @param   string $quote
     * @return  string
     */
    public function serialize($attributes = array(), $valueSeparator = '=', $fieldSeparator = ' ', $quote = '"')
    {
        $data = array();

        foreach ($attributes as $key => $value) {
            $data[] = $key . $valueSeparator . $quote . $value . $quote;
        }
        $res = implode($fieldSeparator, $data);
        return $res;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $html = '<a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getAnchorText()) . '</a>';
        return $html;

    }
}
