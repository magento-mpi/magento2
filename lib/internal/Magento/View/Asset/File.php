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
     * Scope separator for module notation of file ID
     */
    const FILE_ID_SEPARATOR = '::';

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var File\Context
     */
    protected $context;

    /**
     * @var File\Source
     */
    protected $source;

    /**
     * @var string|bool
     */
    private $resolvedFile;

    /**
     * @param File\Source $source
     * @param File\Context $context
     * @param string $fileId
     * @param string $contentType
     */
    public function __construct(File\Source $source, File\Context $context, $fileId, $contentType)
    {
        $this->source = $source;
        $this->context = $context;
        list($this->module, $this->filePath) = self::extractModule($fileId);
        $this->contentType = $contentType;
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->context->getBaseUrl() . $this->getRelativePath();
    }

    /**
     * @inheritdoc
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @inheritdoc
     */
    public function getRelativePath()
    {
        $contextPath = trim($this->context->getPath() . '/' . ($this->module ? $this->module . '/' : ''), '/');
        return ($contextPath ? $contextPath . '/' : '') . $this->filePath;
    }

    /**
     * @inheritdoc
     */
    public function getSourceFile()
    {
        if (null === $this->resolvedFile) {
            $this->resolvedFile = $this->source->getFile($this);
        }
        return $this->resolvedFile;
    }

    /**
     * @inheritdoc
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @inheritdoc
     * @return File\Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get the module context of path (if any)
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Extract module name from specified file ID
     *
     * @param string $fileId
     * @return array
     * @throws \Magento\Exception
     */
    public static function extractModule($fileId)
    {
        if (strpos(str_replace('\\', '/', $fileId), './') !== false) {
            throw new \Magento\Exception("File name '{$fileId}' is forbidden for security reasons.");
        }
        if (strpos($fileId, self::FILE_ID_SEPARATOR) === false) {
            return array('', $fileId);
        }
        $result = explode(self::FILE_ID_SEPARATOR, $fileId, 2);
        if (empty($result[0])) {
            throw new \Magento\Exception('Scope separator "::" cannot be used without scope identifier.');
        }
        return array($result[0], $result[1]);
    }
}
