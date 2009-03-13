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
 * @package    Enterprise_GiftCardAccount
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_GiftCardAccount_Model_Pool extends Enterprise_GiftCardAccount_Model_Pool_Abstract
{
    const CODE_FORMAT_ALPHANUM = 'alphanum';
    const CODE_FORMAT_ALPHA = 'alpha';
    const CODE_FORMAT_NUM = 'num';

    const XML_CONFIG_CODE_FORMAT = 'giftcardaccount/general/code_format';
    const XML_CONFIG_CODE_LENGTH = 'giftcardaccount/general/code_length';
    const XML_CONFIG_CODE_PREFIX = 'giftcardaccount/general/code_prefix';
    const XML_CONFIG_CODE_SUFFIX = 'giftcardaccount/general/code_suffix';
    const XML_CONFIG_CODE_SPLIT  = 'giftcardaccount/general/code_split';

    const XML_CHARSET_NODE      = 'global/enterprise/giftcardaccount/charset/%s';
    const XML_CHARSET_SEPARATOR = 'global/enterprise/giftcardaccount/separator';

    const CODE_GENERATION_ATTEMPTS = 100000;

    protected function _construct()
    {
        $this->_init('enterprise_giftcardaccount/pool');
    }

    public function generatePool()
    {

    }

    /**
     * Generate and save gift card account code
     *
     * @return Enterprise_GiftCardAccount_Model_Pool
     */
    protected function _defineCode()
    {
        $i = 0;
        do {
            if ($i>=self::CODE_GENERATION_ATTEMPTS) {
                Mage::throwException(Mage::helper('enterprise_giftcardaccount')->__('Unique code generation attempts exceeded. Please try again later or cleanup useless entities.'));
            }
            $this->setCode($this->_generateCode());
            $i++;
        } while (!$this->_checkCodeIsUnique());

        return $this;
    }

    /**
     * Generate gift card code
     *
     * @return string
     */
    protected function _generateCode()
    {
        $website = Mage::app()->getWebsite($this->getWebsiteId());

        $format  = $website->getConfig(self::XML_CONFIG_CODE_FORMAT);
        if (!$format) {
            $format = 'alphanum';
        }
        $length  = max(1, (int) $website->getConfig(self::XML_CONFIG_CODE_LENGTH));
        $split   = max(0, (int) $website->getConfig(self::XML_CONFIG_CODE_SPLIT));
        $suffix  = $website->getConfig(self::XML_CONFIG_CODE_SUFFIX);
        $prefix  = $website->getConfig(self::XML_CONFIG_CODE_PREFIX);

        $splitChar = (string) Mage::app()->getConfig()->getNode(self::XML_CHARSET_SEPARATOR);
        $charset = str_split((string) Mage::app()->getConfig()->getNode(sprintf(self::XML_CHARSET_NODE, $format)));

        $code = '';
        for ($i=0; $i<$length; $i++) {
            $char = $charset[array_rand($charset)];
            if ($split > 0 && ($i%$split) == 0 && $i != 0) {
                $char = "{$splitChar}{$char}";
            }
            $code .= $char;
        }

        $code = "{$prefix}{$code}{$suffix}";
        return $code;
    }


    /**
     * Check if current code is unique in database
     *
     * @return bool
     */
    protected function _checkCodeIsUnique()
    {
        $model = Mage::getModel('giftcardaccount/pool')->load($this->getCode());
        if ($model->getId()) {
            return false;
        }
        return true;
    }
}