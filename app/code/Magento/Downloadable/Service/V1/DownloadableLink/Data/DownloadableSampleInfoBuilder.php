<?php
/**
 * Downloadable Link Builder
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\DownloadableLink\Data;

use Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;

class DownloadableSampleInfoBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * @param string $value
     * @return $this
     */
    public function setTitle($value)
    {
        return $this->_set(DownloadableLinkInfo::TITLE, $value);
    }

    /**
     * @param int|null $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(DownloadableLinkInfo::ID, $value);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setSortOrder($value)
    {
        return $this->_set(DownloadableLinkInfo::SORT_ORDER, $value);
    }

    /**
     * File or URL of sample if any
     *
     * @param \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableResourceInfo $sampleResource
     * @return $this
     */
    public function setSampleResource($sampleResource)
    {
        return $this->_set(DownloadableLinkInfo::SAMPLE_RESOURCE, $sampleResource);
    }
}
