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
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Form Input/Output Strip HTML tags Filter
 *
 * @category    Varien
 * @package     Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Filter_Date implements Varien_Data_Form_Filter_Interface
{
    /**
     * Date format
     *
     * @var string
     */
    protected $_dateFormat;

    /**
     * Initialize filter
     *
     * @param string $format    Zend_Date input/output format
     */
    public function __construct($format = null)
    {
        if (is_null($format)) {
            $format = Varien_Date::DATE_INTERNAL_FORMAT;
        }
        $this->_dateFormat = $format;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function inputFilter($value)
    {
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => $this->_dateFormat
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
        ));

        $value = $filterInput->filter($value);
        $value = $filterInternal->filter($value);
        return $value;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function outputFilter($value)
    {
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => $this->_dateFormat
        ));

        return $filterInternal->filter($value);
    }
}
