<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service\Entity\V2;


use Magento\TestModule4\Service\Entity\V1;

class AllSoapAndRest extends V1\AllSoapAndRest
{
    const PRICE = 'price';

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * @param int $price
     * @return AllSoapAndRest
     */
    public function setPrice($price)
    {
        return $this->_set(self::PRICE, $price);
    }

} 