<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account;

/**
 * Customer register link
 */
class RegisterLink extends \Magento\Page\Block\Link
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Customer\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Customer\Model\Session $session,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
        $this->_customerSession = $session;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_helperFactory->get('Magento\Customer\Helper\Data')->getRegisterUrl();
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return '';
        }
        return parent::_toHtml();
    }
}
