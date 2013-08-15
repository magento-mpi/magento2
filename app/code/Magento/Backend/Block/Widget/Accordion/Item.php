<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion item
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Accordion_Item extends Magento_Backend_Block_Widget
{
    protected $_accordion;

    public function setAccordion($accordion)
    {
        $this->_accordion = $accordion;
        return $this;
    }

    public function getTarget()
    {
        return ($this->getAjax()) ? 'ajax' : '';
    }

    public function getTitle()
    {
        $title = $this->getData('title');
        $url = $this->getContentUrl() ? $this->getContentUrl() : '#';
        $title = '<a href="' . $url . '" class="' . $this->getTarget() . '"' . $this->getUiId('title-link') . '>'
            . $title . '</a>';

        return $title;
    }

    public function getContent()
    {
        $content = $this->getData('content');
        if (is_string($content)) {
            return $content;
        }
        if ($content instanceof Magento_Core_Block_Abstract) {
            return $content->toHtml();
        }
        return null;
    }

    public function getClass()
    {
        $class = $this->getData('class');
        if ($this->getOpen()) {
            $class.= ' open';
        }
        return $class;
    }

    protected function _toHtml()
    {
        $content = $this->getContent();
        $html = '<dt id="dt-' . $this->getHtmlId() . '" class="' . $this->getClass() . '"';
        $html .= $this->getUiId() . '>';
        $html .= $this->getTitle();
        $html .= '</dt>';
        $html .= '<dd id="dd-' . $this->getHtmlId() . '" class="' . $this->getClass() . '">';
        $html .= $content;
        $html .= '</dd>';
        return $html;
    }
}
