<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Block representing link with two possible states.
 * "Current" state means link leads to URL equivalent to URL of currently displayed page.
 *
 * @method string                       getLabel()
 * @method string                       getPath()
 * @method string                       getTitle()
 * @method null|bool                    getCurrent()
 * @method Mage_Page_Block_Link_Current setCurrent(bool $value)
 */
class Mage_Page_Block_Link_Current extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'Mage_Page::link/current.phtml';

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
        foreach ($this->_frontController->getDefault() as $key => $defaultValue) {
            $value = isset($routeParts[$key]) ? $routeParts[$key] : $this->_request->getParam($key);
            if (!empty($value) && $value != $defaultValue) {
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
        $currentMca = $this->getMca();
        return $this->getCurrent()
            || $this->getUrl($this->getPath()) == $this->getUrl($currentMca);
    }
}
