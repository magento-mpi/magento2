<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Html notices block
 *
 * @category    Magento
 * @package     Magento_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Page_Block_Html_Notices extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Core_Model_Url
     */
    protected $_urlModel;

    /**
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_urlModel = $urlModel;
        parent::__construct($coreData, $context, $data);
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
