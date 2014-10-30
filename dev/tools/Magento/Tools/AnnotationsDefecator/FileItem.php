<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Tools\AnnotationsDefecator;

class FileItem implements FileItemI
{
    /**
     * Content
     *
     * @var \ArrayObject
     */
    private $content;

    /** @var array  */
    private $contentStructure = [];

    private $fileName;

    /**
     * @param \ArrayObject $content
     * @param LineFactory $lineFactory
     * @param string $fileName
     */
    public function __construct(\ArrayObject $content, LineFactory $lineFactory, $fileName)
    {
        $this->content = $content;
        $this->fileName = $fileName;
        $iterator = $this->content->getIterator();
        while ($iterator->valid()) {
            $this->contentStructure[] = $lineFactory->create($iterator);
            $iterator->next();
        }
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return 0;
    }

    /**
     * @param int $number
     */
    public function setNumber($number)
    {
        return;
    }

    /**
     * Get content as string
     *
     * @return string
     */
    public function getContent()
    {
        $contentString = '';

        /** @var FileItemI $fileItem */
        foreach ($this->contentStructure as $fileItem) {
            $contentString.= $fileItem->getContent();
        }
        return $contentString;
    }

    /**
     * @param int $number
     * @return bool
     */
    public function hasLineNumber($number)
    {
        return array_key_exists($number, $this->content);
    }

    /**
     * @param $number
     * @return FileItemI|null
     */
    public function getStructureHasLineNumber($number)
    {
        /** @var FileItemI $fileItem */
        foreach ($this->contentStructure as $fileItem) {
            if ($fileItem->hasLineNumber($number)) {
                return $fileItem;
            }
        }
        return null;
    }

    /**
     * Get content as list of Line
     *
     * @return array
     */
    public function getContentArray()
    {
        $aggregatedArray = [];
        foreach ($this->contentStructure as $contentItem) {
            $aggregatedArray = array_merge($aggregatedArray, $contentItem->getContentArray());
        }
        return $aggregatedArray;
    }

    /**
     * @param FileItemI $fileItemI
     * @param FileItemI $beforeItem
     */
    public function appendItemBeforeExistingItem(FileItemI $fileItemI, FileItemI $beforeItem)
    {
        $newContentStructure = [];
        foreach ($this->contentStructure as $contentItem) {
            if ($contentItem === $beforeItem) {
                $newContentStructure[] = $fileItemI;
            }
            $newContentStructure[] = $contentItem;
        }
        $this->contentStructure = $newContentStructure;
    }

    /**
     * Reindex content structure line numbers
     */
    public function reindexContentStructure()
    {
        $number = 0;
        /** @var FileItemI $contentItem */
        foreach ($this->contentStructure as $contentItem) {
            $contentItem->setNumber($number);

            if ($contentItem instanceof Annotation) {
                $number = $contentItem->getLastNumber();
            } else {
                $number = $contentItem->getNumber();
            }

            $number++;
        }
    }
}
