<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1\Data\Wrapping;

/**
 * @codeCoverageIgnore
 */
class Image extends \Magento\Framework\Service\Data\AbstractObject
{
    /**#@+
     * Data object properties
     * @var string
     */
    const BASE64_CONTENT = 'base64_content';
    const FILE_NAME = 'file_name';
    /**#@-*/

    /**
     * @return string
     */
    public function getBase64Content()
    {
        return $this->_get(self::BASE64_CONTENT);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->_get(self::FILE_NAME);
    }
}
