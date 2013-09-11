<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Structure\Element;

class Section
    extends \Magento\Backend\Model\Config\Structure\Element\CompositeAbstract
{
    /**
     * Authorization service
     *
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\Core\Model\App $application
     * @param \Magento\Backend\Model\Config\Structure\Element\Iterator $childrenIterator
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(
        \Magento\Core\Model\App $application,
        \Magento\Backend\Model\Config\Structure\Element\Iterator $childrenIterator,
        \Magento\AuthorizationInterface $authorization
    ) {
        parent::__construct($application, $childrenIterator);
        $this->_authorization = $authorization;
    }

    /**
     * Retrieve section header css
     *
     * @return string
     */
    public function getHeaderCss()
    {
        return isset($this->_data['header_css']) ? $this->_data['header_css'] : '';
    }

    /**
     * Check whether section is allowed for current user
     *
     * @return bool
     */
    public function isAllowed()
    {
        return isset($this->_data['resource']) ? $this->_authorization->isAllowed($this->_data['resource']) : false;
    }

    /**
     * Check whether element should be displayed
     *
     * @return bool
     */
    public function isVisible()
    {
        if (!$this->isAllowed()) {
            return false;
        }
        return parent::isVisible();
    }
}
