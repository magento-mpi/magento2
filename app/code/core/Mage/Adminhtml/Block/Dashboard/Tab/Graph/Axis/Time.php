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
 * Adminhtml dashboard tab graph axis abstract
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Adminhtml_Block_Dashboard_Tab_Graph_Axis_Time extends Mage_Adminhtml_Block_Dashboard_Tab_Graph_Axis_Abstract
 {


    protected function _initLabels()
    {
        parent::_initLabels();
        $labelValues = array();


        foreach ($this->getParentBlock()->getAllSeries() as $series) {
            foreach ($this->getCollection() as $item) {
                $time = $series->getValue($item, $this);
                $labelValues[] = $time;
            }
        }

        $labelValues = array_unique($labelValues);
        $timeFormat = $this->getFormat();

        foreach ($labelValues as $value) {
            $date = Mage::getSingleton('core/locale')->date($value, 'yyyy-MM-dd HH:mm:ss');
            $label = $date->toString($timeFormat);
            $this->_labels[] = $this->getLabelText($label);
        }

        return $this;
    }

    public function getFormat()
    {
        if($this->getData('format')) {
            return $this->getData('format');
        }

        switch (strtolower($this->getFormatType())) {
            case "time":
            case "24h":
                return $this->getTimeFormat();
                break;

            case "week":
            case "7d":
            case "1m":
                return $this->getWeekFormat();
                break;

            case "month":
            case "1y":
                return $this->getMonthFormat();
                break;

            case "day":
            default: // Default labels format
                return $this->getDateFormat();
                break;
        }
    }

    public function getTimeFormat()
    {
        if(!$this->getData('time_format')) {
            return 'HH:mm';
        }

        return $this->getData('time_format');
    }

    public function getWeekFormat()
    {
        if(!$this->getData('week_format')) {
            return $this->getDateFormat();
        }

        return $this->getData('week_format');
    }

    public function getMonthFormat()
    {
        if(!$this->getData('month_format')) {
            return 'MMM YYYY';
        }

        return $this->getData('month_format');
    }


    public function getDirection()
    {
        return self::DIRECTION_HORIZONTAL;
    }


    public function getDateFormat()
    {
        if(!$this->getData('date_format')) {
            return Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        }

        return $this->getData('date_format');
    }
 } // Class Mage_Adminhtml_Block_Dashboard_Tab_Graph_Axis_Time end