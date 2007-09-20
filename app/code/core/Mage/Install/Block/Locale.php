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
 * @package    Mage_Install
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Install localization block
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_Block_Locale extends Mage_Install_Block_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('install/locale.phtml');
    }
    
    /**
     * Retrieve locale object
     *
     * @return Zend_Locale
     */
    public function getLocale()
    {
        $locale = $this->getData('locale');
        if (is_null($locale)) {
            $locale = Mage::getSingleton('core/locale')->getLocale();
            $this->setData('locale', $locale);
        }
        return $locale;
    }
    
    /**
     * Retrieve locale data post url
     * 
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('*/*/localePost');
    }
    
    /**
     * Retrieve locale change url
     * 
     * @return string
     */
    public function getChangeUrl()
    {
        return $this->getUrl('*/*/localeChange');
    }
    
    /**
     * Retrieve locale dropdown HTML
     *
     * @return string
     */
    public function getLocaleSelect()
    {
        $html = $this->getLayout()->createBlock('core/html_select')
            ->setName('config[locale]')
            ->setId('locale')
            ->setTitle(__('Locale'))
            ->setClass('required-entry')
            ->setValue($this->getLocale()->__toString())
            ->setOptions(Mage::getSingleton('core/locale')->getOptionLocales())
            ->getHtml();
        return $html;
    }
    
    /**
     * Retrieve timezone dropdown HTML
     *
     * @return string
     */
    public function getTimezoneSelect()
    {
        $html = $this->getLayout()->createBlock('core/html_select')
            ->setName('config[timezone]')
            ->setId('timezone')
            ->setTitle(__('Time Zone'))
            ->setClass('required-entry')
            ->setValue($this->getTimezone())
            ->setOptions(Mage::getSingleton('core/locale')->getOptionTimezones())
            ->getHtml();
        return $html;
    }
    
    /**
     * Retrieve timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return Mage::getSingleton('core/locale')->getTimezone();
    }
    
    /**
     * Retrieve currency dropdown html
     *
     * @return string
     */
    public function getCurrencySelect()
    {
        $html = $this->getLayout()->createBlock('core/html_select')
            ->setName('config[currency]')
            ->setId('currency')
            ->setTitle(__('Default Currency'))
            ->setClass('required-entry')
            ->setValue($this->getCurrency())
            ->setOptions(Mage::getSingleton('core/locale')->getOptionCurrencies())
            ->getHtml();
        return $html;
    }
    
    /**
     * Retrieve currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return Mage::getSingleton('core/locale')->getCurrency();
    }
    
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = new Varien_Object();
            $this->setData('form_data', $data);
        }
        return $data;
    }
}

 