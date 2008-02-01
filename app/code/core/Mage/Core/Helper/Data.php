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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core data helper
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_crypt;

    /**
     * Convert and format price value for current application store
     *
     * @param   float $value
     * @param   bool $format
     * @return  mixed
     */
    public static function currency($value, $format=true)
    {
        try {
            $value = Mage::app()->getStore()->convertPrice($value, $format);
        }
        catch (Exception $e){
            $value = $e->getMessage();
        }
    	return $value;
    }

    /**
     * Format and convert currency using current store option
     *
     * @param   float $value
     * @return  string
     */
    public function formatCurrency($value)
    {
        return $this->currency($value, true);
    }

    /**
     * Format date using current locale options
     *
     * @param   date $date
     * @param   string $format
     * @param   bool $showTime
     * @return  string
     */
    public function formatDate($date=null, $format='short', $showTime=false)
    {
        if (Mage_Core_Model_Locale::FORMAT_TYPE_FULL    !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_LONG    !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM  !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT   !==$format) {
            return $date;
        }

        if (is_null($date)) {
            $date = Mage::app()->getLocale()->date(time());
        }
        else {
            $date = Mage::app()->getLocale()->date(strtotime($date));
        }

        if ($showTime) {
            $format = Mage::app()->getLocale()->getDateTimeFormat($format);
        }
        else {
            $format = Mage::app()->getLocale()->getDateFormat($format);
        }

        return $date->toString($format);
    }

    /**
     * Format time using current locale options
     *
     * @param   date $date
     * @param   string $format
     * @param   bool $showTime
     * @return  string
     */
    public function formatTime($time=null, $format='short', $showDate=false)
    {
        if (Mage_Core_Model_Locale::FORMAT_TYPE_FULL    !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_LONG    !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM  !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT   !==$format) {
            return $date;
        }

        if (is_null($time)) {
            $date = Mage::app()->getLocale()->date(time());
        }
        else {
            $date = Mage::app()->getLocale()->date(strtotime($time));
        }

        if ($showDate) {
            $format = Mage::app()->getLocale()->getDateTimeFormat($format);
        }
        else {
            $format = Mage::app()->getLocale()->getTimeFormat($format);
        }

        return $date->toString($format);
    }

    /**
     * Encrypt data using application key
     *
     * @param   string $data
     * @return  string
     */
    public function encrypt($data)
    {
        if (!Mage::app()->isInstalled()) {
            return $data;
        }
        $result = base64_encode($this->_getCrypt()->encrypt($data));
        return $result;
    }

    /**
     * Decrypt data using application key
     *
     * @param   string $data
     * @return  string
     */
    public function decrypt($data)
    {
        if (!Mage::app()->isInstalled()) {
            return $data;
        }
        $result = trim($this->_getCrypt()->decrypt(base64_decode($data)));
        return $result;
    }

    public function validateKey($key)
    {
        return $this->_getCrypt($key);
    }

    protected function _getCrypt($key=null)
    {
        if (!$this->_crypt) {
            if (is_null($key)) {
                $key = (string)Mage::getConfig()->getNode('global/crypt/key');
            }
            $this->_crypt = Varien_Crypt::factory()->init($key);
        }
        return $this->_crypt;
    }

    /**
     * Retrieve store identifier
     *
     * @param   mixed $store
     * @return  int
     */
    public function getStoreId($store=null)
    {
        return Mage::app()->getStore($store)->getId();
    }

    public function removeAccents($string, $german=false)
    {
        // Single letters
        $single_fr = explode(" ", "À Á Â Ã Ä Å &#260; &#258; Ç &#262; &#268; &#270; &#272; Ð È É Ê Ë &#280; &#282; &#286; Ì Í Î Ï &#304; &#321; &#317; &#313; Ñ &#323; &#327; Ò Ó Ô Õ Ö Ø &#336; &#340; &#344; Š &#346; &#350; &#356; &#354; Ù Ú Û Ü &#366; &#368; Ý Ž &#377; &#379; à á â ã ä å &#261; &#259; ç &#263; &#269; &#271; &#273; è é ê ë &#281; &#283; &#287; ì í î ï &#305; &#322; &#318; &#314; ñ &#324; &#328; ð ò ó ô õ ö ø &#337; &#341; &#345; &#347; š &#351; &#357; &#355; ù ú û ü &#367; &#369; ý ÿ ž &#378; &#380;");
        $single_to = explode(" ", "A A A A A A A A C C C D D D E E E E E E G I I I I I L L L N N N O O O O O O O R R S S S T T U U U U U U Y Z Z Z a a a a a a a a c c c d d e e e e e e g i i i i i l l l n n n o o o o o o o o r r s s s t t u u u u u u y y z z z");
        $single = array();
        for ($i=0; $i<count($single_fr); $i++) {
            $single[$single_fr[$i]] = $single_to[$i];
        }

        // Ligatures
        $ligatures = array("Æ"=>"Ae", "æ"=>"ae", "Œ"=>"Oe", "œ"=>"oe", "ß"=>"ss");
        // German umlauts
        $umlauts = array("Ä"=>"Ae", "ä"=>"ae", "Ö"=>"Oe", "ö"=>"oe", "Ü"=>"Ue", "ü"=>"ue");

        // Join replaces
        $replacements = array_merge($single, $ligatures);
        if ($german) {
            $replacements = array_merge($replacements, $umlauts);
        }

        // convert string from default database format (UTF-8)
        // to encoding which replacement arrays made with (ISO-8859-1)
        $string = iconv('UTF-8', 'ISO-8859-1', $string);

        // Replace
        $string = strtr($string, $replacements);

        return $string;
    }
}
