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
        $single_fr = explode(" ", "� � � � � � &#260; &#258; � &#262; &#268; &#270; &#272; � � � � � &#280; &#282; &#286; � � � � &#304; &#321; &#317; &#313; � &#323; &#327; � � � � � � &#336; &#340; &#344; � &#346; &#350; &#356; &#354; � � � � &#366; &#368; � � &#377; &#379; � � � � � � &#261; &#259; � &#263; &#269; &#271; &#273; � � � � &#281; &#283; &#287; � � � � &#305; &#322; &#318; &#314; � &#324; &#328; � � � � � � � &#337; &#341; &#345; &#347; � &#351; &#357; &#355; � � � � &#367; &#369; � � � &#378; &#380;");
        $single_to = explode(" ", "A A A A A A A A C C C D D D E E E E E E G I I I I I L L L N N N O O O O O O O R R S S S T T U U U U U U Y Z Z Z a a a a a a a a c c c d d e e e e e e g i i i i i l l l n n n o o o o o o o o r r s s s t t u u u u u u y y z z z");
        $single = array();
        for ($i=0; $i<count($single_fr); $i++) {
            $single[$single_fr[$i]] = $single_to[$i];
        }

        // Ligatures
        $ligatures = array("�"=>"Ae", "�"=>"ae", "�"=>"Oe", "�"=>"oe", "�"=>"ss");
        // German umlauts
        $umlauts = array("�"=>"Ae", "�"=>"ae", "�"=>"Oe", "�"=>"oe", "�"=>"Ue", "�"=>"ue");

        // Join replaces
        $replacements = array_merge($single, $ligatures);
        if ($german) {
            $replacements = array_merge($replacements, $umlauts);
        }

        // convert string from default database format (UTF-8)
        // to encoding which replacement arrays made with (ISO-8859-1)

        //        Notice: iconv() [function.iconv]: Detected an illegal character in input string in /var/www/magento/app/code/core/Mage/Core/Helper/Data.php on line 207
        //        [0] in iconv("UTF-8", "ISO-8859-1", "Ноутбуки") in /var/www/magento/app/code/core/Mage/Core/Helper/Data.php on line 207
        //        [1] in Mage_Core_Helper_Data->removeAccents("Ноутбуки") in /var/www/magento/app/code/core/Mage/Catalog/Model/Category.php on line 308
        //        [2] in Mage_Catalog_Model_Category->formatUrlKey("Ноутбуки") in /var/www/magento/app/code/core/Mage/Catalog/Model/Entity/Category/Attribute/Backend/Urlkey.php on line 41
        //        [3] in Mage_Catalog_Model_Entity_Category_Attribute_Backend_Urlkey->beforeSave(Mage_Catalog_Model_Category)
        //        [4] in call_user_func_array(Array[2], Array[1]) in /var/www/magento/app/code/core/Mage/Eav/Model/Entity/Abstract.php on line 552
        //        [5] in Mage_Eav_Model_Entity_Abstract->walkAttributes("backend/beforeSave", Array[1]) in /var/www/magento/app/code/core/Mage/Eav/Model/Entity/Abstract.php on line 1179
        //        [6] in Mage_Eav_Model_Entity_Abstract->_beforeSave(Mage_Catalog_Model_Category) in /var/www/magento/app/code/core/Mage/Catalog/Model/Entity/Category.php on line 81
        //        [7] in Mage_Catalog_Model_Entity_Category->_beforeSave(Mage_Catalog_Model_Category) in /var/www/magento/app/code/core/Mage/Eav/Model/Entity/Abstract.php on line 778
        //        [8] in Mage_Eav_Model_Entity_Abstract->save(Mage_Catalog_Model_Category) in /var/www/magento/app/code/core/Mage/Catalog/Model/Category.php on line 138
        //        [9] in Mage_Catalog_Model_Category->save() in /var/www/magento/app/code/core/Mage/Adminhtml/controllers/Catalog/CategoryController.php on line 183
        //        [10] in Mage_Adminhtml_Catalog_CategoryController->saveAction() in /var/www/magento/app/code/core/Mage/Core/Controller/Varien/Action.php on line 337
        //        [11] in Mage_Core_Controller_Varien_Action->dispatch("save") in /var/www/magento/app/code/core/Mage/Core/Controller/Varien/Router/Admin.php on line 141
        //        [12] in Mage_Core_Controller_Varien_Router_Admin->match(Mage_Core_Controller_Request_Http) in /var/www/magento/app/code/core/Mage/Core/Controller/Varien/Front.php on line 147
        //        [13] in Mage_Core_Controller_Varien_Front->dispatch() in /var/www/magento/app/Mage.php on line 381
        //        [14] in Mage::run("base") in /var/www/magento/index.php on line 28
        #$string = iconv('UTF-8', 'ISO-8859-1', $string);

        // Replace
        $string = strtr($string, $replacements);

        return $string;
    }
}
