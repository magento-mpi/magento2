<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Quick search weight model
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Weight
{
    /**
     * Quick search weights
     *
     * @var array
     */
    static $weights = array(
        1,
        2,
        3,
        4,
        5
    );

    /**
     * Retrieve search weights as options array
     *
     * @return array
     */
    static public function getOptions()
    {
        $res = array();
        foreach (self::getValues() as $value) {
            $res[] = array(
               'value' => $value,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * Retrieve search weights array
     *
     * @return array
     */
    static public function getValues()
    {
        return self::$weights;
    }
}
