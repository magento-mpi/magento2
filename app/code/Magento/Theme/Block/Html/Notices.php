<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Block\Html;

/**
 * Html page notices block
 */
class Notices extends \Magento\View\Element\Template
{
    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\View\Element\Template\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
    }

    /**
     * Check if noscript notice should be displayed
     *
     * @return boolean
     */
    public function displayNoscriptNotice()
    {
        return $this->_storeConfig->getValue(
            'web/browser_capabilities/javascript',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if demo store notice should be displayed
     *
     * @return boolean
     */
    public function displayDemoNotice()
    {
        return $this->_storeConfig->getValue(
            'design/head/demonotice',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Link to cookie restriction privacy policy page
     *
     * @return string
     */
    public function getPrivacyPolicyLink()
    {
        return $this->_urlBuilder->getUrl('privacy-policy-cookie-restriction-mode');
    }
}
