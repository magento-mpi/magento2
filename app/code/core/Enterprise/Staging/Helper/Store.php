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
 * Staging store helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Helper_Store extends Mage_Core_Helper_Url
{
    const XML_PATH_STAGING_CODE_SUFFIX   = 'global/enterprise/staging/staging_store_code_suffix';

    /**
     * Cache for store rewrite suffix
     *
     * @var array
     */
    protected $_stagingCodeSuffix = null;

    /**
     * Retrieve store code sufix
     *
     * @return string
     */
    public function getStoreCodeSuffix()
    {
        if (is_null($this->_stagingCodeSuffix)) {
            $this->_stagingCodeSuffix = (string) Mage::getConfig()->getNode(self::XML_PATH_STAGING_CODE_SUFFIX);
        }
        return $this->_stagingCodeSuffix;
    }

    /**
     * Retrieve free (non-used) store code with code suffix (if specified in config)
     *
     * @param   string $code
     * @return  string
     */
    public function generateStoreCode($code)
    {
        return $this->getUnusedStoreCode($code) . $this->getStoreCodeSuffix();
    }

    /**
     * Retrieve free (non-used) store code
     *
     * @param   string $code
     * @return  string
     */
    public function getUnusedStoreCode($code)
    {
        if (empty($code)) {
            $code = '_';
        } elseif ($code == $this->getStoreCodeSuffix()) {
            $code = '_' . $this->getStoreCodeSuffix();
        }

        try {
            $store = Mage::app()->getStore($code);
        } catch (Exception $e) {
            $store = false;
        }
        if ($store) {
            // retrieve code suffix for staging stores
            $storeCodeSuffix = $this->getStoreCodeSuffix();

            $match = array();
            if (!preg_match('#^([0-9a-z_]+?)(_([0-9]+))?('.preg_quote($storeCodeSuffix).')?$#i', $code, $match)) {
                return $this->getUnusedStoreCode('_');
            }
            $code = $match[1].(isset($match[3])?'_'.($match[3]+1):'_1').(isset($match[4])?$match[4]:'');
            return $this->getUnusedStoreCode($code);
        } else {
            return $code;
        }
    }
}
