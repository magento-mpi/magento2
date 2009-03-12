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
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_GiftCardAccount_Model_Giftcardaccount extends Mage_Core_Model_Abstract
{
    const CODE_FORMAT_ALPHANUM = 'alphanum';
    const CODE_FORMAT_ALPHA = 'alpha';
    const CODE_FORMAT_NUM = 'num';

    const XML_CONFIG_CODE_FORMAT = 'giftcardaccount/general/code_format';
    const XML_CONFIG_CODE_LENGTH = 'giftcardaccount/general/code_length';
    const XML_CONFIG_CODE_PREFIX = 'giftcardaccount/general/code_prefix';
    const XML_CONFIG_CODE_SUFFIX = 'giftcardaccount/general/code_suffix';
    const XML_CONFIG_CODE_SPLIT  = 'giftcardaccount/general/code_split';

    const XML_CHARSET_NODE = 'global/enterprise/giftcardaccount/charset/%s';

    const XML_CHARSET_SEPARATOR = 'global/enterprise/giftcardaccount/separator';

    const CODE_GENERATION_ATTEMPTS = 20;


    protected function _construct()
    {
        $this->_init('enterprise_giftcardaccount/giftcardaccount');
    }


    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $now = Mage::app()->getLocale()->date()
                    ->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)
                    ->toString(Varien_Date::DATE_INTERNAL_FORMAT);

            $this->setDateCreated($now);
            $this->_defineCode();
        }

        if ($this->getDateExpires()) {
            $this->setDateExpires(
                Mage::app()->getLocale()->date(
                    $this->getDateExpires(),
                    Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                    null,
                    false
                )->toString(Varien_Date::DATE_INTERNAL_FORMAT)
            );
        } else {
            $this->setDateExpires(null);
        }

        parent::_beforeSave();
    }


    /**
     * Generate and save gift card account code
     *
     * @return Enterprise_GiftCardAccount_Model_Giftcardaccount
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
        if ($this->_getResource()->getIdByCode($this->getCode())) {
            return false;
        }
        return true;
    }


    /**
     * Load gift card account model using specified code
     *
     * @param string $code
     * @return Enterprise_GiftCardAccount_Model_Giftcardaccount
     */
    public function loadByCode($code)
    {
        return $this->load($code, 'code');
    }


    /**
     * Add gift card to quote gift card storage
     *
     * @param bool $saveQuote
     * @return Enterprise_GiftCardAccount_Model_Giftcardaccount
     */
    public function addToCart($saveQuote = true)
    {
        if (!$this->getId()) {
            Mage::throwException(Mage::helper('enterprise_giftcardaccount')->__('Gift Card was not found.'));
        }
        if ($this->isExpired()) {
            Mage::throwException(Mage::helper('enterprise_giftcardaccount')->__('Gift Card expired.'));
        }
        if ($this->getBalance() <= 0) {
            Mage::throwException(Mage::helper('enterprise_giftcardaccount')->__('There is no funds on this Gift Card.'));
        }

        $cards = Mage::helper('enterprise_giftcardaccount')->getCards($this->_getCheckoutSession()->getQuote());
        if (!$cards) {
            $cards = array();
        } else {
            foreach ($cards as $one) {
                if ($one['i'] == $this->getId()) {
                    Mage::throwException(Mage::helper('enterprise_giftcardaccount')->__('This Gift Card is already in your cart.'));
                }
            }
        }
        $cards[] = array(
            'i'=>$this->getId(),        // id
            'c'=>$this->getCode(),      // code
            'a'=>$this->getBalance(),   // amount
            'ba'=>$this->getBalance(),  // base amount
        );
        Mage::helper('enterprise_giftcardaccount')->setCards($this->_getCheckoutSession()->getQuote(), $cards);

        if ($saveQuote) {
            $this->_getCheckoutSession()->getQuote()->save();
        }

        return $this;
    }

    /**
     * Remove gift card from quote gift card storage
     *
     * @param bool $saveQuote
     * @return Enterprise_GiftCardAccount_Model_Giftcardaccount
     */
    public function removeFromCart($saveQuote = true)
    {
        if (!$this->getId()) {
            Mage::throwException(Mage::helper('enterprise_giftcardaccount')->__('Gift Card was not found.'));
        }

        $cards = Mage::helper('enterprise_giftcardaccount')->getCards($this->_getCheckoutSession()->getQuote());
        if ($cards) {
            foreach ($cards as $k=>$one) {
                if ($one['i'] == $this->getId()) {
                    unset($cards[$k]);
                    Mage::helper('enterprise_giftcardaccount')->setCards($this->_getCheckoutSession()->getQuote(), $cards);

                    if ($saveQuote) {
                        $this->_getCheckoutSession()->getQuote()->save();
                    }
                    return $this;
                }
            }
        }

        Mage::throwException(Mage::helper('enterprise_giftcardaccount')->__('Gift Card was not found.'));
    }

    /**
     * Return checkout/session model singleton
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Check if this gift card is expired at the moment
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->getDateExpires()) {
            return false;
        }

        $currentDate = strtotime(Mage::getModel('core/date')->date('Y-m-d'));

        if (strtotime($this->getDateExpires()) < $currentDate) {
            return true;
        }
        return false;
    }
}