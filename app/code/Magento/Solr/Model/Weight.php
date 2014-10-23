<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model;

/**
 * Quick search weight model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Weight
{
    /**
     * Quick search weights
     *
     * @var int[]
     */
    protected static $weights = array(1, 2, 3, 4, 5);

    /**
     * Retrieve search weights as options array
     *
     * @return array
     */
    public static function getOptions()
    {
        $res = array();
        foreach (self::getValues() as $value) {
            $res[] = array('value' => $value, 'label' => $value);
        }
        return $res;
    }

    /**
     * Retrieve search weights array
     *
     * @return int[]
     */
    public static function getValues()
    {
        return self::$weights;
    }
}
