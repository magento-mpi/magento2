<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\Data;

interface FileContentUploaderInterface
{
    /**
     * Upload provided downloadable file content
     *
     * @param FileContent $fileContent
     * @param string $contentType
     * @return array
     * @throws \InvalidArgumentException
     */
    public function upload(FileContent $fileContent, $contentType);
}
