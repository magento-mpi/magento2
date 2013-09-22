<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Centinel validation frame
 */
namespace Magento\Centinel\Block;

class Authentication extends \Magento\Core\Block\Template
{
    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Strage for identifiers of related blocks
     *
     * @var array
     */
    protected $_relatedBlocks = array();

    /**
     * Flag - authentication start mode
     * @see self::setAuthenticationStartMode
     *
     * @var bool
     */
    protected $_authenticationStartMode = false;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Add identifier of related block
     *
     * @param string $blockId
     * @return \Magento\Centinel\Block\Authentication
     */
    public function addRelatedBlock($blockId)
    {
        $this->_relatedBlocks[] = $blockId;
        return $this;
    }

    /**
     * Return identifiers of related blocks
     *
     * @return array
     */
    public function getRelatedBlocks()
    {
        return $this->_relatedBlocks;
    }

    /**
     * Check whether authentication is required and prepare some template data
     *
     * @return string
     */
    protected function _toHtml()
    {
        $method = $this->_checkoutSession->getQuote()->getPayment()->getMethodInstance();
        if ($method->getIsCentinelValidationEnabled()) {
            $centinel = $method->getCentinelValidator();
            if ($centinel && $centinel->shouldAuthenticate()) {
                $this->setAuthenticationStart(true);
                $this->setFrameUrl($centinel->getAuthenticationStartUrl());
                return parent::_toHtml();
            }
        }
        return parent::_toHtml();
    }
}
