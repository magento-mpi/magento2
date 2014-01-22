<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Guest;

/**
 * "Orders and Returns" link
 */
class Link extends \Magento\View\Element\Html\Link\Current
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\App\DefaultPathInterface $defaultPath
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_customerSession = $customerSession;
        $this->_isScopePrivate = true;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return '';
        }
        return parent::_toHtml();
    }
}
