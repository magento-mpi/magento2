<?php
/**
 * Application file system directories dictionary
 *
 * Provides information about what directories are available in the application
 * Serves as customizaiton point to specify different directories or add own
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem;

use Magento\App\Dir;

class DirectoryList extends Dir
{
    /**
     * Custom application dirs
     */
    const PARAM_APP_DIRS = 'app_dirs';

    /**
     * Custom application uris
     */
    const PARAM_APP_URIS = 'app_uris';

    /**
     * Code base root
     */
    const ROOT = 'base';

    /**
     * Most of entire application
     */
    const APP = 'app';

    /**
     * Modules
     */
    const MODULES = 'code';

    /**
     * Themes
     */
    const THEMES = 'design';

    /**
     * Initial configuration of the application
     */
    const CONFIG = 'etc';

    /**
     * Libraries or third-party components
     */
    const LIB = 'lib';

    /**
     * Files with translation of system labels and messages from en_US to other languages
     */
    const LOCALE = 'i18n';

    /**
     * \Directory within document root of a web-server to access static view files publicly
     */
    const PUB = 'pub';

    /**
     * Libraries/components that need to be accessible publicly through web-server (such as various DHTML components)
     */
    const PUB_LIB = 'pub_lib';

    /**
     * Storage of files entered or generated by the end-user
     */
    const MEDIA = 'media';

    /**
     * Storage of static view files that are needed on HTML-pages, emails or similar content
     */
    const STATIC_VIEW = 'static';

    /**
     * Public view files, stored to avoid repetitive run-time calculation, and can be re-generated any time
     */
    const PUB_VIEW_CACHE = 'view_cache';

    /**
     * Various files generated by the system in runtime
     */
    const VAR_DIR = 'var';

    /**
     * Temporary files
     */
    const TMP = 'tmp';

    /**
     * File system caching directory (if file system caching is used)
     */
    const CACHE = 'cache';

    /**
     * Logs of system messages and errors
     */
    const LOG = 'log';

    /**
     * File system session directory (if file system session storage is used)
     */
    const SESSION = 'session';

    /**
     * Dependency injection related file directory
     *
     */
    const DI = 'di';

    /**
     * Relative directory key for generated code
     */
    const GENERATION = 'generation';

    /**
     * Temporary directory for uploading files by end-user
     */
    const UPLOAD = 'upload';

    /**
     * Root path
     *
     * @var string
     */
    protected $root;

    /**
     * Directories configurations
     *
     * @var array
     */
    protected $directories = array(
        self::ROOT          => array('path' => ''),
        self::APP           => array('path' => 'app'),
        self::MODULES       => array('path' => 'app/code'),
        self::THEMES        => array('path' => 'app/design'),
        self::CONFIG        => array('path' => 'app/etc'),
        self::LIB           => array('path' => 'lib'),
        self::VAR_DIR       => array('path' => 'var'),
        self::TMP           => array('path' => 'var/tmp'),
        self::CACHE         => array('path' => 'var/cache'),
        self::LOG           => array('path' => 'var/log'),
        self::SESSION       => array('path' => 'var/session'),
        self::DI            => array('path' => 'var/di'),
        self::GENERATION    => array('path' => 'var/generation'),
        self::PUB           => array('path' => 'pub'),
        self::PUB_LIB       => array('path' => 'pub/lib'),
        self::MEDIA         => array('path' => 'pub/media'),
        self::UPLOAD        => array('path' => 'pub/media/upload'),
        self::STATIC_VIEW   => array('path' => 'pub/static'),
        self::PUB_VIEW_CACHE => array('path' => 'pub/cache')
    );

    /**
     * @param string $root
     * @param array $uris
     * @param array $dirs
     */
    public function __construct($root, array $uris = array(), array $dirs = array())
    {
        parent::__construct($root, $uris, $dirs);
        $this->root = $root;

        foreach ($this->directories as $code => $configuration) {
            $this->directories[$code]['path'] = $this->makeAbsolute($configuration['path']);
        }

        foreach ($dirs as $code => $path) {
            $this->directories[$code]['path'] = $path;
        }
        foreach ($this->_getDefaultReplacements($dirs) as $code => $replacement) {
            $this->directories[$code]['path'] = $replacement;
        }

        foreach ($uris as $code => $uri) {
            $this->directories[$code]['uri'] = $uri;
        }
        foreach ($this->_getDefaultReplacements($uris) as $code => $replacement) {
            $this->directories[$code]['uri'] = $replacement;
        }
    }

    /**
     * Add directory configuration
     *
     * @param string $code
     * @param array $configuration
     */
    public function addDirectory($code, $configuration)
    {
        $configuration['path'] = $this->makeAbsolute($configuration['path']);
        if (isset($configuration['read_only'])) {
            $configuration['read_only'] = $configuration['read_only'] == 'true' ? true : false;
        }
        $this->directories[$code] = $configuration;
    }

    /**
     * Add root dir for relative path
     *
     * @param string $path
     * @return string
     */
    protected function makeAbsolute($path)
    {
        return $this->getRoot() . '/' . $path;
    }

    /**
     * Retrieve root path
     *
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Get configuration for directory code
     *
     * @param string $code
     * @return array
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function getConfig($code)
    {
        if (!isset($this->directories[$code])) {
            throw new \Magento\Filesystem\FilesystemException(
                sprintf('The "%s" directory is not specified in configuration', $code)
            );
        }
        return $this->directories[$code];
    }
}
