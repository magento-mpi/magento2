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
class Link extends \Magento\Page\Block\Link
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_customerSession = $customerSession;
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
