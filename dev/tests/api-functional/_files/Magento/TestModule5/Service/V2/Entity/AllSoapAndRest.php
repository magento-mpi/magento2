<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service\V2\Entity;

use Magento\TestModule5\Service\Entity\V1;

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
}
