<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Model;

class DateFactory
{
    /**
     * @param  string|integer|\Zend_Date|array  $date    OPTIONAL Date value or value of date part to set
     *                                                 ,depending on $part. If null the actual time is set
     * @param  string                          $part    OPTIONAL Defines the input format of $date
     * @param  string|\Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return \Zend_Date
     */
    public function create($date = null, $part = null, $locale = null)
    {
        return new \Zend_Date($date, $part, $locale);
    }

} 
