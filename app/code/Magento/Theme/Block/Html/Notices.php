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
     * @var \Magento\Core\Model\Url
     */
    protected $_urlModel;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Url $urlModel,
        array $data = array()
    ) {
        $this->_urlModel = $urlModel;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Check if noscript notice should be displayed
     *
     * @return boolean
     */
    public function displayNoscriptNotice()
    {
        return $this->_storeConfig->getConfig('web/browser_capabilities/javascript');
    }

    /**
     * Check if demo store notice should be displayed
     *
     * @return boolean
     */
    public function displayDemoNotice()
    {
        return $this->_storeConfig->getConfig('design/head/demonotice');
    }

    /**
     * Get Link to cookie restriction privacy policy page
     *
     * @return string
     */
    public function getPrivacyPolicyLink()
    {
        return $this->_urlModel->getUrl('privacy-policy-cookie-restriction-mode');
    }
}
