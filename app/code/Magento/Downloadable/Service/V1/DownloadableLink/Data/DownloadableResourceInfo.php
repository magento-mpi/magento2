<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\DownloadableLink\Data;

use \Magento\Framework\Service\Data\AbstractObject;

class DownloadableResourceInfo extends AbstractObject
{
    const FILE = 'file';

    const URL = 'url';

    const TYPE = 'type';

    /**
     * Return file path or null when type is 'url'
     *
     * @return string|null relative file path
     */
    public function getFile()
    {
        return $this->_get(self::FILE);
    }

    /**
     * Return URL or NULL when type is 'file'
     *
     * @return string|null file URL
     */
    public function getUrl()
    {
        return $this->_get(self::URL);
    }

    /**
     * Possible types are 'file' and 'url'
     *
     * @return string
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }
}