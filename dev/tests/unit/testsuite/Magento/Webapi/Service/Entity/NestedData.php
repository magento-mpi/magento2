<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Service\Data\AbstractObject;

class NestedData extends AbstractObject
{
    /**
     * @return \Magento\Webapi\Service\Entity\SimpleData
     */
    public function getDetails()
    {
        return $this->_get('details');
    }
}
