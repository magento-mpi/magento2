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
 * @method string                          getLabel()
 * @method string                          getPath()
 * @method string                          getTitle()
 * @method null|bool                       getCurrent()
 * @method Magento_Page_Block_Link_Current setCurrent(bool $value)
 */
class Magento_Page_Block_Link_Current extends Magento_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Page::link/current.phtml';

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
        $dafaultsParams = $this->_frontController->getDefault();

        $parts = array();
        foreach ($routeParts as $key => $value) {
            if (!empty($value) && (!isset($dafaultsParams[$key]) || $value != $dafaultsParams[$key])) {
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
}
