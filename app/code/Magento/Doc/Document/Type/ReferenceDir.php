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
 * Class ReferenceDir
 * @package Magento\Doc\Document\Type
 */
class ReferenceDir extends AbstractType implements ReferenceInterface
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
        if ($dir->isExist($path)) {
            $flags = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS;
            $iterator = new \RecursiveDirectoryIterator($dir->getAbsolutePath($path), $flags);
            $result = "<label>{$path}</label>";
            $result .= $this->buildTree($iterator);
        } else {
            throw new \InvalidArgumentException("Missed target path reference: " . $item->getData('reference'));
        }
        return $result;
    }

    /**
     * Build directory tree html
     *
     * @param $parent
     * @return string
     */
    protected function buildTree($parent)
    {
        $flags = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS;
        $result = '<ul class="directory-tree">';
        foreach ($parent as $item) {
            /** @var \SplFileInfo $item */
            if ($item->isDir()) {
                $result .= '<li>';
                $result .= '<label>' . $item->getBasename() . '</label>';
                $result .= $this->buildTree(
                    new \RecursiveDirectoryIterator($item, $flags)
                );
                $result .= '</li>';
            }
        }
        $result .= '</ul>';
        return $result;
    }
}
