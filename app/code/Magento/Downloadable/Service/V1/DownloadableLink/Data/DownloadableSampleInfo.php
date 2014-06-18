<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\DownloadableLink\Data;

class DownloadableSampleInfo extends AbstractObject
{
    CONST ID = 'id';

    CONST TITLE = 'title';

    CONST SORT_ORDER = 'sort_order';

    CONST SAMPLE_RESOURCE = 'sample_resource';

    /**
     * Product sample id
     *
     * @return int|null Sample(or link) id
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Sample title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * File or URL of sample
     *
     * @return \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableResourceInfo
     */
    public function getSampleResource()
    {
        return $this->_get(self::SAMPLE_RESOURCE);
    }

    /**
     * Sort order index for sample
     *
     * @return mixed
     */
    public function getSortOrder()
    {
        return $this->_get(self::SORT_ORDER);
    }
}