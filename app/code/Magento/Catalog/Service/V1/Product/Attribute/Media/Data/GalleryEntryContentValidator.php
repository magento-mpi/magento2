<?php
/**
 * Product Media Content Validator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media\Data;

use Magento\Framework\Exception\InputException;

class GalleryEntryContentValidator
{
    /**
     * @var array
     */
    private $defaultMimeTypes = [
        'image/jpg',
        'image/jpeg',
        'image/gif',
        'image/png',
    ];

    /**
     * @var array
     */
    private $allowedMimeTypes;

    /**
     * @param array $allowedMimeTypes
     */
    public function __construct(
        array $allowedMimeTypes = []
    ) {
        $this->allowedMimeTypes = array_merge($this->defaultMimeTypes, $allowedMimeTypes);
    }

    /**
     * Check if gallery entry content is valid
     *
     * @param GalleryEntryContent $entryContent
     * @return bool
     * @throws InputException
     */
    public function isValid(GalleryEntryContent $entryContent)
    {
        $fileContent = @base64_decode($entryContent->getData(), true);
        if (empty($fileContent)) {
            throw new InputException('The image content must be valid base64 encoded data.');
        }
        $imageProperties = @getimagesizefromstring($fileContent);
        if (empty($imageProperties)) {
            throw new InputException('The image content must be valid base64 encoded data.');
        }
        $sourceMimeType = $imageProperties['mime'];
        if ($sourceMimeType != $entryContent->getMimeType() || !$this->isMimeTypeValid($sourceMimeType)) {
            throw new InputException('The image MIME type is not valid or not supported.');
        }
        if (!$this->isNameValid($entryContent->getName())) {
            throw new InputException('Provided image name contains forbidden characters.');
        }
        return true;
    }

    /**
     * Check if given mime type is valid
     *
     * @param string $mimeType
     * @return bool
     */
    protected function isMimeTypeValid($mimeType)
    {
        return in_array($mimeType, $this->allowedMimeTypes);
    }

    /**
     * Check if given filename is valid
     *
     * @param string $name
     * @return bool
     */
    protected function isNameValid($name)
    {
        // Cannot contain \ / : * ? " < > |
        if (!preg_match('/^[^\\/?*:";<>()|{}\\\\]+$/', $name)) {
            return false;
        }
        return true;
    }
}
