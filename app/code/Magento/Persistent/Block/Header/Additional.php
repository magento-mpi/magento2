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

class Additional extends \Magento\Core\Block\Html\Link
{
    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Persistent\Helper\Session $persistentSession,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_persistentSession = $persistentSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Render additional header html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $text = __('(Not %1?)', $this->escapeHtml($this->_persistentSession->getCustomer()->getName()));

        $this->setAnchorText($text);
        $this->setHref($this->getUrl('persistent/index/unsetCookie'));

        return parent::_toHtml();
    }
}
