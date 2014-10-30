<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Tools\AnnotationsDefecator;

use Magento\Framework\Config\FileIterator;
use Symfony\Component\Finder\Tests\Iterator\Iterator;

class FileItemFactory
{
    /**
     * Creates File item
     *
     * @param string $fileName
     * @return FileItem
     * @throws \Exception File doesn't exist
     */
    public function create($fileName)
    {
        if (!is_file($fileName)) {
            throw new \Exception(sprintf('File %s doesn\'t exist', (string)$fileName));
        }

        return new FileItem(
            new \ArrayObject(file($fileName, FILE_IGNORE_NEW_LINES)), new LineFactory(), (string)$fileName
        );
    }
}
