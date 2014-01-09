<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Remember Me block
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Persistent\Block\Header;

class Additional extends \Magento\View\Element\Html\Link
{
    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Persistent\Helper\Session $persistentSession,
        array $data = array()
    ) {
        $this->_persistentSession = $persistentSession;
        parent::__construct($context, $data);
    }

    /*
     * Retrieve unset cookie link
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('persistent/index/unsetCookie');
    }

    /**
     * Render additional header html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $text = __('(Not %1?)', $this->escapeHtml($this->_persistentSession->getCustomer()->getName()));

        return '<span><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($text) . '</a></span>';
    }
}
