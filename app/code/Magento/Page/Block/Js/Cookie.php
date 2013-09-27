<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Page_Block_Js_Cookie extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Core_Model_Cookie
     */
    protected $_cookie;

    /**
     * @param Magento_Core_Model_Cookie $cookie
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Cookie $cookie,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_cookie = $cookie;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get cookie model instance
     *
     * @return Magento_Core_Model_Cookie
     */
    public function getCookie()
    {
        return $this->_cookie;
    }
    /**
     * Get configured cookie domain
     *
     * @return string
     */
    public function getDomain()
    {
        $domain = $this->getCookie()->getDomain();
        if (!empty($domain[0]) && ($domain[0] !== '.')) {
            $domain = '.'.$domain;
        }
        return $domain;
    }

    /**
     * Get configured cookie path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getCookie()->getPath();
    }
}
