<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element\Html;

/**
 * Links list block
 */
class Links extends \Magento\View\Element\Template
{
    /**
     * Get links
     *
     * @return \Magento\View\Element\Html\Link[]
     */
    public function getLinks()
    {
        return $this->_layout->getChildBlocks($this->getNameInLayout());
    }

    /**
     * Render Block
     *
     * @param \Magento\View\Element\AbstractBlock $link
     * @return string
     */
    public function renderLink(\Magento\View\Element\AbstractBlock $link)
    {
        return $this->_layout->renderElement($link->getNameInLayout());
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

        $html = '';
        if ($this->getLinks()) {
            $html = '<ul' . ($this->hasCssClass()?' class="' . $this->escapeHtml($this->getCssClass()) . '"':'') . '>';
            foreach ($this->getLinks() as $link) {
                $html .= $this->renderLink($link);
            }
            $html .= '</ul>';
        }

        return $html;
    }
}
