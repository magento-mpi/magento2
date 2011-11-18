<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Staging website helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Helper_Website extends Mage_Core_Helper_Url
{
    const XML_PATH_STAGING_CODE_SUFFIX   = 'global/enterprise/staging/staging_website_code_suffix';

    /**
     * Cache for website rewrite suffix
     *
     * @var array
     */
    protected $_stagingCodeSuffix = null;

    /**
     * Retrieve website code sufix
     *
     * @return string
     */
    public function getWebsiteCodeSuffix()
    {
        if (is_null($this->_stagingCodeSuffix)) {
            $this->_stagingCodeSuffix = (string) Mage::getConfig()->getNode(self::XML_PATH_STAGING_CODE_SUFFIX);
        }
        return $this->_stagingCodeSuffix;
    }

    /**
     * Retrieve free (non-used) website code with code suffix (if specified in config)
     *
     * @param   string $code
     * @return  string
     */
    public function generateWebsiteCode($code)
    {
        return $this->getUnusedWebsiteCode($code) . $this->getWebsiteCodeSuffix();
    }

    /**
     * Retrieve free (non-used) website code
     *
     * @param   string $code
     * @return  string
     */
    public function getUnusedWebsiteCode($code)
    {
        if (empty($code)) {
            $code = '_';
        } elseif ($code == $this->getWebsiteCodeSuffix()) {
            $code = '_' . $this->getWebsiteCodeSuffix();
        }

        try {
            $website = Mage::app()->getWebsite($code);
        } catch (Exception $e) {
            $website = false;
        }
        if ($website) {
            // retrieve code suffix for staging websites
            $websiteCodeSuffix = $this->getWebsiteCodeSuffix();

            $match = array();
            if (!preg_match('#^([0-9a-z_]+?)(_([0-9]+))?('.preg_quote($websiteCodeSuffix).')?$#i', $code, $match)) {
                return $this->getUnusedWebsiteCode('_');
            }
            $code = $match[1].(isset($match[3])?'_'.($match[3]+1):'_1').(isset($match[4])?$match[4]:'');
            return $this->getUnusedWebsiteCode($code);
        } else {
            return $code;
        }
    }
}
