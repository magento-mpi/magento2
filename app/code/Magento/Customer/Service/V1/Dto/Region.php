<?php
/**
 * Class Region
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

class Region extends \Magento\Service\Entity\AbstractDto
{
    /**
     * @return string
     */
    public function getRegionCode()
    {
        return $this->_get('region_code');
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->_get('region');
    }

    /**
     * @return int
     */
    public function getRegionId()
    {
        return $this->_get('region_id');
    }
}
