<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block;

use Magento\Framework\View\Element\Template;

class QuoteShortcutButtons extends \Magento\Catalog\Block\ShortcutButtons
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        parent::__construct($context, false, null, $data);
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Dispatch shortcuts container event
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_eventManager->dispatch(
            'shortcut_buttons_container',
            array(
                'container' => $this,
                'is_catalog_product' => $this->_isCatalogProduct,
                'or_position' => $this->_orPosition,
                'checkout_session' => $this->_checkoutSession
            )
        );
        return $this;
    }
}
