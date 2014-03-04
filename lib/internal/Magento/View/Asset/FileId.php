<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

use Magento\View;

/**
 * A locally available static view file asset that can be referred with a "file ID" and has "design parameters"
 */
class FileId extends File
{
    /**
     * Scope separator for module notation of file ID
     */
    const FILE_ID_SEPARATOR = '::';

    /**
     * @var PathGenerator
     */
    protected $pathGenerator;

    /**
     * @var SourceFileInterface
     */
    protected $fileSource;

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $area;

    /**
     * @var string
     */
    protected $themePath;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param PathGenerator $pathGenerator
     * @param SourceFileInterface $fileSource
     * @param string $fileId
     * @param string $baseUrl
     * @param string $areaCode
     * @param string $themePath
     * @param string $localeCode
     * @throws \LogicException
     */
    public function __construct(
        PathGenerator $pathGenerator,
        SourceFileInterface $fileSource,
        $fileId,
        $baseUrl,
        $areaCode,
        $themePath,
        $localeCode
    ) {
        $this->pathGenerator = $pathGenerator;
        $this->fileSource = $fileSource;
        list($this->module, $filePath) = self::extractModule($fileId);
        $this->area = $areaCode;
        $this->themePath = $themePath;
        $this->locale = $localeCode;
        parent::__construct($filePath, null, $baseUrl);
    }

    /**
     * @inheritdoc
     * @throws \LogicException
     */
    public function getSourceFile()
    {
        // note that this in-memory caching is consistent only as long as the object is immutable!
        if (null === $this->file) {
            $this->file = $this->fileSource->getSourceFile($this);
            if (false === $this->file) {
                throw new \LogicException("Unable to resolve the source file for '{$this->getRelativePath()}'");
            }
        }
        return $this->file;
    }

    /**
     * @inheritdoc
     */
    public function getRelativePath()
    {
        $filePath = parent::getFilePath();
        $relPath = $this->pathGenerator->getPath($this->area, $this->themePath, $this->locale, $this->module);
        return $relPath . '/' . $filePath;
    }

    /**
     * Get module path part extracted from "file ID"
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getAreaCode()
    {
        return $this->area;
    }

    /**
     * @return string
     */
    public function getThemePath()
    {
        return $this->themePath;
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->locale;
    }

    /**
     * Spawn a new instance of self class with all the same parameter and internal state, but a different fileId
     *
     * @param string $fileId
     * @return FileId
     */
    public function createSimilar($fileId)
    {
        return new FileId(
            $this->pathGenerator,
            $this->fileSource,
            $fileId,
            $this->baseUrl,
            $this->getAreaCode(),
            $this->getThemePath(),
            $this->getLocaleCode()
        );
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
