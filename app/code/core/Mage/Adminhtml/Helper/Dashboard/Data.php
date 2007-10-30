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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Data helper for dashboard
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Helper_Dashboard_Data extends Mage_Core_Helper_Data
{
    protected $_websites = null;
    protected $_locale = null;
    protected $_stores = array();

    public function getWebsites()
    {
        if(is_null($this->_websites)) {
            $this->_websites = Mage::getModel('core/website')->getResourceCollection()
                ->load();
        }

        return $this->_websites;
    }

    public function getStores($website)
    {
        if(!isset($this->_stores[$website->getId()])) {
            $this->_stores[$website->getId()] = $website->getStoreCollection()->load();
        }

        return $this->_stores[$website->getId()];
    }

    public function countStores($stores)
    {
        return sizeof($stores->getItems());
    }

    public function getConfig($section, $index=null)
    {
        $data = Mage::getSingleton('adminhtml/session')->getDashboardData();

        if(isset($data[$section])) {
            if (!is_null($index) && isset($data[$section][$index])) {
                return $data[$section][$index];
            } elseif (!is_null($index)) {
                return null;
            }
            return $data[$section];
        }

        return array();
    }

    public function getDatePeriods()
    {
        return array(
            '24h'=>$this->__('Last 24 hours'),
            '7d'=>$this->__('Last 7 days'),
		    '1m'=>$this->__('Last Month'),
		    '1y'=>$this->__('Last Year'),
		    'custom'=>$this->__('Custom...')
        );
    }

    public function getDateCustomValue($field, $section)
    {
        $locale = $this->getConfig($section, 'locale');

        if(!$locale) {
            $locale = $this->getLocaleCode();
        }

        if($value = $this->getConfig($section, 'custom_' . $field)) {
            return $this->getLocale()->date($value, Zend_Date::DATE_SHORT, $locale);
        }

        return null;
    }

    public function getSectionData($section)
    {
        return array(
            'store'=>$this->getConfig($section, 'store'),
            'range'=>$this->getConfig($section, 'range') ? $this->getConfig($section, 'range') : '24h',
            'custom_from' => (string) $this->getDateCustomValue('from', $section),
            'custom_to'   => (string) $this->getDateCustomValue('to', $section)
        );
    }

    public function getDateCustomValueEscaped($field, $section)
    {
        $locale = $this->getConfig($section, 'locale');

        if(!$locale) {
            $locale = $this->getLocaleCode();
        }

        $value = $this->getConfig($section, 'custom_' . $field);

        return $this->htmlEscape($value);
    }

    /**
     * Retrieve locale
     *
     * @return Mage_Core_Model_Locale
     */
    public function getLocale()
    {
        if (!$this->_locale) {
            $this->_locale = Mage::app()->getLocale();
        }
        return $this->_locale;
    }

}// Class Mage_Adminhtml_Helper_Dashboard_Data END