<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Block\Header;

/**
 * Remember Me block
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Additional extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Persistent\Helper\Session $persistentSession,
        array $data = array()
    ) {
        $this->_persistentSession = $persistentSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
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
