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
 * Locale model
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Locale
{
    /**
     * Default locale name
     */
    const DEFAULT_LOCALE = 'en_US';
    
    /**
     * Locale object
     *
     * @var Zend_Locale
     */
    protected $_locale;
    
    public function __construct($locale = null) 
    {
        $this->setLocale($locale);
    }
    
    /**
     * Set locale
     *
     * @param   strint $locale
     * @return  Mage_Core_Model_Locale
     */
    public function setLocale($locale = null)
    {
        $locale = Mage::getStoreConfig('locale');
        if (!$locale) {
            $locale = self::DEFAULT_LOCALE;
        }
        $this->_locale = new Zend_Locale($locale);
        
        /**
         * @todo retrieve timezone from config
         */
        date_default_timezone_set('America/Los_Angeles');
        return $this;
    }
    
    /**
     * Retrieve locale object
     *
     * @return Zend_Locale
     */
    public function getLocale()
    {
        if (!$this->_locale) {
            $this->setLocale();
        }
        
        return $this->_locale;
    }
    
    /**
     * Retrieve locale code
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->getLocale()->toString();
    }
}
