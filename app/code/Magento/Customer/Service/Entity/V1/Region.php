<?php
/**
 * Class Region
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1;

use Magento\Service\Entity\AbstractDto;

class Region extends AbstractDto
{
    /**
     * @param string $regionCode
     * @param string $regionName
     * @param int $regionId
     */
    public function __construct($regionCode = '', $regionName = '', $regionId = 0)
    {
        parent::__construct();
        $this->setRegionCode($regionCode);
        $this->setRegion($regionName);
        $this->setRegionId($regionId);
    }

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

    /**
     * @param string $regionCode
     * @return Region
     */
    public function setRegionCode($regionCode)
    {
        return $this->_set('region_code', $regionCode);
    }

    /**
     * @param string $regionName
     * @return Region
     */
    public function setRegion($regionName)
    {
        return $this->_set('region', $regionName);
    }

    /**
     * @param int $regionId
     * @return Region
     */
    public function setRegionId($regionId)
    {
        return $this->_set('region_id', $regionId);
    }
}
