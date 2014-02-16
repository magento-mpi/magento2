<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element\Html\Link;

/**
 * Block representing link with two possible states.
 * "Current" state means link leads to URL equivalent to URL of currently displayed page.
 *
 * @method string                          getLabel()
 * @method string                          getPath()
 * @method string                          getTitle()
 * @method null|bool                       getCurrent()
 * @method \Magento\View\Element\Html\Link\Current setCurrent(bool $value)
 */
class Current extends \Magento\View\Element\Template
{
    /**
     * Default path
     *
     * @var \Magento\App\DefaultPathInterface
     */
    protected $_defaultPath;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\App\DefaultPathInterface $defaultPath
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\App\DefaultPathInterface $defaultPath,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_defaultPath = $defaultPath;
    }


    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl($this->getPath());
    }

    /**
     * Get current mca
     *
     * @return string
     */
    private function getMca()
    {
        $routeParts = array(
            'module' => $this->_request->getModuleName(),
            'controller' => $this->_request->getControllerName(),
            'action' => $this->_request->getActionName(),
        );

        $parts = array();
        foreach ($routeParts as $key => $value) {
            if (!empty($value) && ($value != $this->_defaultPath->getPart($key))) {
                $parts[] = $value;
            }
        }
        return implode('/', $parts);
    }

    /**
     * Check if link leads to URL equivalent to URL of currently displayed page
     *
     * @return bool
     */
    public function isCurrent()
    {
        return $this->getCurrent()
            || $this->getUrl($this->getPath()) == $this->getUrl($this->getMca());
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

        $highlight = '';

        if ($this->getIsHighlighted()) {
            $highlight = ' current';
        }

        if ($this->isCurrent()) {
            $html = '<li class="nav item current">';
            $html .= '<strong>' . $this->escapeHtml(__($this->getLabel())) . '</strong>';
            $html .= '</li>';
        } else {
            $html = '<li class="nav item' . $highlight . '"><a href="'. $this->escapeHtml($this->getHref()) . '"';
            $html .= ($this->getTitle()?' title="' . $this->escapeHtml(__($this->getTitle())) . '"':'');
            $html .= '>' . $this->escapeHtml(__($this->getLabel())) . '</a></li>';
        }

        return $html;
    }
}
