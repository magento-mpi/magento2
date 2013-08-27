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

class Magento_Persistent_Block_Header_Additional extends Magento_Core_Block_Html_Link
{
    /**
     * Persistent session
     *
     * @var Magento_Persistent_Helper_Session
     */
    protected $_persistentSession = null;

    /**
     * @param Magento_Persistent_Helper_Session $persistentSession
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Persistent_Helper_Session $persistentSession,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_persistentSession = $persistentSession;
        parent::__construct($context, $data);
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
