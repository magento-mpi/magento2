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
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Credit card payment method model
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Payment_Model_Cc extends Mage_Payment_Model_Abstract
{
    /**
     * Retrieve credit card types available for method
     * 
     * array(
     *  [$code] => $name
     * )
     * 
     * @return array
     */
    public function getCcTypes()
    {
        $data = array();
        foreach (Mage::getConfig()->getNode(self::XML_PATH_CC_TYPES) as $type) {
        	$data[(string)$type->code] = (string)$type->name;
        }
        return $data;
    }
    
    /**
     * Retrieve list of months translation
     *
     * @return array
     */
    public function getMonths()
    {
        return Mage::app()->getLocale()->getLocale()->getTranslationList('month');
    }
    
    /**
     * Retrieve array of available years
     *
     * @return array
     */
    public function getYears()
    {
        $years = array();
        $first = date("Y");
        
        for ($index=0; $index<10; $index++) {
            $year = $first + $index;
            $years[$year] = $year;
        }
        return $years;
    }
}
