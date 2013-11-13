<?php
/**
 * Renders HTML anchor or nothing depending on isVisible().
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use \Magento\Object;

class Link extends AbstractRenderer
{
    /** @var \Magento\Object */
    protected $_row;

    /**
     * Render grid row
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(Object $row)
    {
        $this->_row = $row;

        if (!$this->isVisible($row)) {
            return '';
        }

        $html = sprintf(
            '<a href="%s" %s>%s</a>',
            $this->_getUrl($row),
            $this->getAdditionalHtmlParameters(),
            $this->getCaption()
        );

        return $html;
    }

    /**
     * Decide whether anything should be rendered.
     *
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * Decide whether action associated with the link is not available.
     *
     * @return bool
     */
    public function isDisabled()
    {
        return true;
    }

    /**
     * Return URL pattern for action associated with the link e.g. "(star)(slash)(star)(slash)activate" ->
     * will be translated to http://.../admin/integration/activate/id/X
     *
     * @return string
     */
    public function getUrlPattern()
    {
        return $this->getColumn()->getUrlPattern();
    }

    /**
     * Caption for the link.
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->isDisabled()
            ? $this->getColumn()->getDisabledCaption() ?: $this->getColumn()->getCaption()
            : $this->getColumn()->getCaption();
    }

    /**
     * Return additional HTML parameters for tag, e.g. 'style'
     *
     * @return string
     */
    public function getAdditionalHtmlParameters()
    {
        return sprintf('title="%s"', $this->getCaption());
    }

    /**
     * Render URL for current item.
     *
     * @param \Magento\Object $row
     * @return string
     */
    protected function _getUrl(Object $row)
    {
        return $this->isDisabled($row) ? '#' : $this->getUrl($this->getUrlPattern(), ['id' => $row->getId()]);
    }
}
