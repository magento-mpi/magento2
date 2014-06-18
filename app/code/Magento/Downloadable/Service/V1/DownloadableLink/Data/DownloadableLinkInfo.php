<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\DownloadableLink\Data;

use Magento\Framework\Service\Data\AbstractObject;

class DownloadableLinkInfo extends AbstractObject
{
    CONST ID = 'id';

    CONST TITLE = 'title';

    CONST SORT_ORDER = 'sort_order';

    CONST SHARABLE = 'sharable';

    CONST PRICE = 'price';

    CONST NUMBER_OF_DOWNLOADS = 'number_of_downloads';

    CONST SAMPLE_RESOURCE = 'sample_resource';

    CONST LINK_RESOURCE = 'link_resource';

    /**
     * Product link id
     *
     * @return int|null Sample(or link) id
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Link title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Sort order index for link
     *
     * @return int
     */
    public function getSortOrder()
    {
        return (int)$this->_get(self::SORT_ORDER);
    }

    /**
     * Link sharable status
     * 0 -- No
     * 1 -- Yes
     * 2 -- Use config default value
     *
     * @return int
     */
    public function getSharable()
    {
        return (int)$this->_get(self::SHARABLE);
    }

    /**
     * Link price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Number of downloads per user
     * Null for unlimited downloads
     *
     * @return int|null
     */
    public function getNumberOfDownloads()
    {
        return $this->_get(self::NUMBER_OF_DOWNLOADS);
    }

    /**
     * File or URL of sample if any
     *
     * @return \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableResourceInfo|null
     */
    public function getSampleResource()
    {
        return $this->_get(self::SAMPLE_RESOURCE);
    }

    /**
     * File or URL of link
     *
     * @return \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableResourceInfo
     */
    public function getLinkResource()
    {
        return $this->_get(self::LINK_RESOURCE);
    }
}