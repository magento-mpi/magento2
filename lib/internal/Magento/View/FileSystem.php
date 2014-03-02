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
     * Model, used to resolve the file paths
     *
     * @var \Magento\View\Design\FileResolution\StrategyPool
     */
    protected $_resolutionPool;

    /**
     * View service
     *
     * @var Service
     */
    protected $_viewService;

    /**
     * Constructor
     *
     * @param \Magento\View\Design\FileResolution\StrategyPool $resolutionPool
     * @param Service $viewService
     */
    public function __construct(
        \Magento\View\Design\FileResolution\StrategyPool $resolutionPool,
        Service $viewService
    ) {
        $this->_resolutionPool = $resolutionPool;
        $this->_viewService = $viewService;
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
        $filePath = $this->_viewService->extractScope($this->normalizePath($fileId), $params);
        $this->_viewService->updateDesignParams($params);
        return $this->_resolutionPool->getFileStrategy(!empty($params['skipProxy']))
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
        $this->_viewService->updateDesignParams($params);
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        return $this->_resolutionPool->getLocaleStrategy($skipProxy)->getLocaleFile(
            $params['area'],
            $params['themeModel'],
            $params['locale'],
            $file
        );
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
        $asset = $this->_viewService->createAsset($fileId, $params);
        return $asset->getSourceFile();
    }

    /**
     * Notify that view file resolved path was changed (i.e. it was published to a public directory)
     *
     * @param Publisher\FileInterface $publisherFile
     * @return $this
     */
    public function notifyViewFileLocationChanged(Publisher\FileInterface $publisherFile)
    {
        $params = $publisherFile->getViewParams();
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        $strategy = $this->_resolutionPool->getViewStrategy($skipProxy);
        if ($strategy instanceof Design\FileResolution\Strategy\View\NotifiableInterface) {
            /** @var $strategy Design\FileResolution\Strategy\View\NotifiableInterface  */
            $strategy->setViewFilePathToMap(
                $params['area'],
                $params['themeModel'],
                $params['locale'],
                $params['module'],
                $publisherFile->getFilePath(),
                $publisherFile->buildPublicViewFilename()
            );
        }

        return $this;
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
     * Get a relative path between $from and $to paths as if $from was to refer to $to relatively of itself
     *
     * Returns new calculated relative path.
     * Examples:
     *   /some/directory/one/file.ext -> /some/directory/two/another/file.ext
     *       Result: ../two/another
     *   http://example.com/themes/demo/css/styles.css -> http://example.com/images/logo.gif
     *       Result: ../../../images
     *
     * @param string $from
     * @param string $to
     * @return string
     */
    public static function offsetPath($from, $to)
    {
        list($from, $to) = self::ltrimSamePart($from, $to);
        $offset = str_repeat('../', count(explode('/', ltrim(dirname($to), '/'))));
        return rtrim($offset . dirname($from), '/');
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
