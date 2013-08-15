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
 * @method string getLabel()
 * @method string getPath()
 * @method string getTitle()
 */
class Mage_Page_Block_Link_Current extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'link/current.phtml';

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl($this->getPath());
    }

    /**
     * Check if link leads to URL equivalent to URL of currently displayed page
     *
     * @return bool
     */
    public function isCurrent()
    {
        return implode('/', explode('/', rtrim($this->getPath(), '/')) + array('', 'index', 'index'))
            === $this->_frontController->getAction()->getFullActionName('/');
    }
}
