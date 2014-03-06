<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

/**
 * Quick search weight model
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Weight
{
    /**
     * Quick search weights
     *
     * @var int[]
     */
    static protected $weights = array(
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
     * @return int[]
     */
    static public function getValues()
    {
        return self::$weights;
    }
}
