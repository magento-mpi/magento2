<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
    protected static $weights = [1, 2, 3, 4, 5];

    /**
     * Retrieve search weights as options array
     *
     * @return array
     */
    public static function getOptions()
    {
        $res = [];
        foreach (self::getValues() as $value) {
            $res[] = ['value' => $value, 'label' => $value];
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
