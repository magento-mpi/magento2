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

class Section extends AbstractComposite
{
    /**
     * Authorization service
     *
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Iterator $childrenIterator
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Iterator $childrenIterator,
        \Magento\AuthorizationInterface $authorization
    ) {
        parent::__construct($storeManager, $childrenIterator);
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
