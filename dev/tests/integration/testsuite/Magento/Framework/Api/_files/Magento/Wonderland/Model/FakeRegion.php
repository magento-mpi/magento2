<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Wonderland\Model;

use Magento\Wonderland\Api\Data\FakeRegionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class FakeRegion extends AbstractExtensibleModel implements FakeRegionInterface
{

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * Get region code
     *
     * @return string
     */
    public function getRegionCode()
    {
        return $this->getData(self::REGION_CODE);
    }

    /**
     * Get region id
     *
     * @return int
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }
}