<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document\Type;

use Magento\Doc\Document\Content;
use Magento\Doc\Document\Item;
use Magento\Framework\Filesystem;

/**
 * Class ReferenceFile
 * @package Magento\Doc\Document\Type
 */
class ReferenceFile extends AbstractType implements ReferenceInterface
{
    /**
     * @var Content
     */
    protected $content;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * Constructor
     *
     * @param Content $content
     * @param Filesystem $filesystem
     */
    public function __construct(
        Content $content,
        Filesystem $filesystem
    ) {
        $this->content = $content;
        $this->filesystem = $filesystem;
    }

    /**
     * Get content
     *
     * @param Item $item
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getContent(Item $item)
    {
        list ($dirType, $path) = explode('::', $item->getData('reference'));
        $dir = $this->filesystem->getDirectoryRead($dirType);
        if ($dir->isFile($path)) {
            $content = $dir->readFile($path);
            $result = "<label>{$path}</label>";
            $result .= '<xmp class="prettyprint">' . $content . '</xmp>';
        } else {
            throw new \InvalidArgumentException("Missed target file path reference: " . $item->getData('reference'));
        }
        return $result;
    }
}
