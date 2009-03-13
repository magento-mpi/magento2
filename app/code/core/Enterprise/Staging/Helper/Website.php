<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * Cache for product rewrite suffix
     *
     * @var array
     */
    protected $_stagingCodeSuffix = null;

    /**
     * Retrieve product view page url
     *
     * @param   mixed $product
     * @return  string
     */
    public function getProductUrl($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            return $product->getProductUrl();
        }
        elseif (is_numeric($product)) {
            return Mage::getModel('catalog/product')->load($product)->getProductUrl();
        }
        return false;
    }

    /**
     * Check if a website can be shown
     *
     * @param  Enterprise_Staging_Model_Staging_Website|int $website
     * @return boolean
     */
    public function canShow($website, $where = 'frontend')
    {
        if (is_int($website)) {
            $website = Mage::getModel('enterprise_staging/staging_website')->load($website);
        }
        /* @var $staging Enterprise_Staging_Model_Staging_Website */

        if (!$website->getId()) {
            return false;
        }

        return $website->isVisibleOnFrontend();
    }

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
}