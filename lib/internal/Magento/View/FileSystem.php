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
     * File paths resolver
     *
     * @var \Magento\View\Design\FileResolution\Fallback
     */
    protected $_viewFileResolution;

    /**
     * View service
     *
     * @var Asset\Service
     */
    protected $_assetService;

    /**
     * Constructor
     *
     * @param \Magento\View\Design\FileResolution\Fallback $fallback
     * @param Asset\Service $assetService
     */
    public function __construct(
        Design\FileResolution\Fallback $fallback,
        Asset\Service $assetService
    ) {
        $this->_viewFileResolution = $fallback;
        $this->_assetService = $assetService;
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
        $filePath = $this->_assetService->extractScope($this->normalizePath($fileId), $params);
        $this->_assetService->updateDesignParams($params);
        return $this->_viewFileResolution
            ->getFile($params['area'], $params['themeModel'], $filePath, $params['module']);
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
        $this->_assetService->updateDesignParams($params);
        return $this->_viewFileResolution
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
        $asset = $this->_assetService->createAsset($fileId, $params);
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
