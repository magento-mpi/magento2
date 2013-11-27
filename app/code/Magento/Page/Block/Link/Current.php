<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Block\Link;

/**
 * Block representing link with two possible states.
 * "Current" state means link leads to URL equivalent to URL of currently displayed page.
 *
 * @method string                          getLabel()
 * @method string                          getPath()
 * @method string                          getTitle()
 * @method null|bool                       getCurrent()
 * @method \Magento\Page\Block\Link\Current setCurrent(bool $value)
 */
class Current extends \Magento\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Page::link/current.phtml';

    /**
     * Default path
     *
     * @var \Magento\App\DefaultPathInterface
     */
    protected $_defaultPath;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\App\DefaultPathInterface $defaultPath
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\App\DefaultPathInterface $defaultPath,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
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
}
