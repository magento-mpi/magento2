<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * Model that finds file paths by their fileId
 */
class FileSystem
{
    /**
     * @var Design\FileResolution\Fallback\File
     */
    protected $_fileResolution;

    /**
     * @var Design\FileResolution\Fallback\LocaleFile
     */
    protected $_localeFileResolution;

    /**
     * View service
     *
     * @var Asset\Repository
     */
    protected $_assetRepo;

    /**
     * Constructor
     *
     * @param Design\FileResolution\Fallback\File $fallbackFile
     * @param Design\FileResolution\Fallback\LocaleFile $fallbackLocaleFile
     * @param Asset\Repository $assetRepo
     */
    public function __construct(
        Design\FileResolution\Fallback\File $fallbackFile,
        Design\FileResolution\Fallback\LocaleFile $fallbackLocaleFile,
        Asset\Repository $assetRepo
    ) {
        $this->_fileResolution = $fallbackFile;
        $this->_localeFileResolution = $fallbackLocaleFile;
        $this->_assetRepo = $assetRepo;
    }

    /**
     * Get existing file name with fallback to default
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getFilename($fileId, array $params = array())
    {
        $filePath = $this->_assetRepo->extractScope($this->normalizePath($fileId), $params);
        $this->_assetRepo->updateDesignParams($params);
        $file = $this->_fileResolution
            ->getFile($params['area'], $params['themeModel'], $filePath, $params['module']);
        return $file;
    }

    /**
     * Get a locale file
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getLocaleFileName($file, array $params = array())
    {
        $this->_assetRepo->updateDesignParams($params);
        return $this->_localeFileResolution
            ->getLocaleFile($params['area'], $params['themeModel'], $params['locale'], $file);
    }

    /**
     * Find a view file using fallback mechanism
     *
     * @param string $fileId
     * @param array $params
     * @return string|bool
     */
    public function getViewFile($fileId, array $params = array())
    {
        $asset = $this->_assetRepo->createAsset($fileId, $params);
        return $asset->getSourceFile();
    }

    /**
     * Remove excessive "." and ".." parts from a path
     *
     * For example foo/bar/../file.ext -> foo/file.ext
     *
     * @param string $path
     * @return string
     */
    public static function normalizePath($path)
    {
        $parts = explode('/', $path);
        $result = array();

        foreach ($parts as $part) {
            if ('..' === $part) {
                if (!count($result) || ($result[count($result) - 1] == '..')) {
                    $result[] = $part;
                } else {
                    array_pop($result);
                }
            } elseif ('.' !== $part) {
                $result[] = $part;
            }
        }
        return implode('/', $result);
    }

    /**
     * Get a relative path between $relatedPath and $path paths as if $path was to refer to $relatedPath
     * relatively of itself
     *
     * Returns new calculated relative path.
     * Examples:
     *   $path: /some/directory/one/file.ext
     *   $relatedPath: /some/directory/two/another/file.ext
     *   Result: ../two/another
     *
     *   $path: http://example.com/themes/demo/css/styles.css
     *   $relatedPath: http://example.com/images/logo.gif
     *   Result: ../../../images
     *
     * @param string $relatedPath
     * @param string $path
     * @return string
     */
    public static function offsetPath($relatedPath, $path)
    {
        list($relatedPath, $path) = self::ltrimSamePart($relatedPath, $path);
        $toDir = ltrim(dirname($path), '/');
        if ($toDir == '.') {
            $offset = '';
        } else {
            $offset = str_repeat('../', count(explode('/', $toDir)));
        }
        return rtrim($offset . dirname($relatedPath), '/');
    }

    /**
     * Left-trim same part of two paths
     *
     * @param string $pathOne
     * @param string $pathTwo
     * @return array
     */
    private static function ltrimSamePart($pathOne, $pathTwo)
    {
        $one = explode('/', $pathOne);
        $two = explode('/', $pathTwo);
        while (isset($one[0]) && isset($two[0]) && $one[0] == $two[0]) {
            array_shift($one);
            array_shift($two);
        }
        return array(implode('/', $one), implode('/', $two));
    }
}
