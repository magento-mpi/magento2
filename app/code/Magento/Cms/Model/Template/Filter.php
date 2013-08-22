<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms Template Filter Model
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Model_Template_Filter extends Magento_Core_Model_Email_Template_Filter
{
    /**
     * Whether to allow SID in store directive: AUTO
     *
     * @var bool
     */
    protected $_useSessionInUrl = null;

    /**
     * Setter whether SID is allowed in store directive
     *
     * @param bool $flag
     * @return Magento_Cms_Model_Template_Filter
     */
    public function setUseSessionInUrl($flag)
    {
        $this->_useSessionInUrl = (bool)$flag;
        return $this;
    }
}
