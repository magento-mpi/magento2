<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Block;

/**
 * Check result block for a Giftcardaccount
 */
class Check extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get current card instance from registry
     *
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    public function getCard()
    {
        return $this->_coreRegistry->registry('current_giftcardaccount');
    }

    /**
     * Check whether a gift card account code is provided in request
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getRequest()->getParam('giftcard-code', '');
    }

    /**
     * {@inheritdoc}
     */
    public function formatDate(
        $date = null,
        $format = \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT,
        $showTime = false
    ) {
        if (is_string($date)) {
            $date = $this->_localeDate->date(strtotime($date), null, null, false);
        }

        return parent::formatDate($date, $format, $showTime);
    }
}
