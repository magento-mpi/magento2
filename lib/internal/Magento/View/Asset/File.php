<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * A locally available static view file asset that can be referred with a file path
 */
class File implements MergeableInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $sourceFile;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @param string $filePath
     * @param string $sourceFile
     * @param string $baseUrl
     * @throws \LogicException
     */
    public function __construct($filePath, $sourceFile, $baseUrl)
    {
        $this->filePath = $filePath;
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!$extension) {
            throw new \LogicException("An extension is expected in file path: {$filePath}");
        }
        $this->contentType = $extension;
        $this->sourceFile = $sourceFile;
        $this->baseUrl = $baseUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->baseUrl . $this->getRelativePath();
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceFile()
    {
        return $this->sourceFile;
    }

    /**
     * @inheritdoc
     */
    public function getRelativePath()
    {
        return $this->filePath;
    }

    /**
     * Getter for file path
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
}
