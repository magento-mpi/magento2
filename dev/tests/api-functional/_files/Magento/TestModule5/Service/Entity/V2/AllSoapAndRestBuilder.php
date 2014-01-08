<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service\Entity\V2;

use Magento\TestModule5\Service\Entity\V1;

class AllSoapAndRestBuilder extends V1\AllSoapAndRestBuilder
{
    const PRICE = 'price';

    /**
     * @param int $price
     * @return \Magento\TestModule5\Service\Entity\V2\AllSoapAndRestBuilder
     */
    public function setPrice($price)
    {
        return $this->_set(self::PRICE, $price);
    }
}
