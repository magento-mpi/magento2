<?php
/**
 * Created by PhpStorm.
 * User: flammus
 * Date: 4/15/14
 * Time: 2:16 PM
 */

namespace Magento\Catalog\Pricing\Price;


class Stub extends AbstractPrice
{
    public function getValue()
    {
        $examplePrice = 77;
        return $examplePrice;
    }
} 