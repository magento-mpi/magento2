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
        \Magento\Filesystem::ROOT          => array('path' => ''),
        \Magento\Filesystem::APP           => array('path' => 'app'),
        \Magento\Filesystem::MODULES       => array('path' => 'app/code'),
        \Magento\Filesystem::THEMES        => array('path' => 'app/design'),
        \Magento\Filesystem::CONFIG        => array('path' => 'app/etc'),
        \Magento\Filesystem::LIB           => array('path' => 'lib'),
        \Magento\Filesystem::VAR_DIR       => array('path' => 'var'),
        \Magento\Filesystem::TMP           => array('path' => 'var/tmp'),
        \Magento\Filesystem::CACHE         => array('path' => 'var/cache'),
        \Magento\Filesystem::LOG           => array('path' => 'var/log'),
        \Magento\Filesystem::SESSION       => array('path' => 'var/session'),
        \Magento\Filesystem::DI            => array('path' => 'var/di'),
        \Magento\Filesystem::GENERATION    => array('path' => 'var/generation'),
        \Magento\Filesystem::SOCKET        => array('path' => null),
        \Magento\Filesystem::PUB           => array('path' => 'pub'),
        \Magento\Filesystem::PUB_LIB       => array('path' => 'pub/lib'),
        \Magento\Filesystem::MEDIA         => array('path' => 'pub/media'),
        \Magento\Filesystem::UPLOAD        => array('path' => 'pub/media/upload'),
        \Magento\Filesystem::STATIC_VIEW   => array('path' => 'pub/static'),
        \Magento\Filesystem::PUB_VIEW_CACHE => array('path' => 'pub/cache'),
        \Magento\Filesystem::LOCALE          => array('path' => '')
    );

    /**
     * @param string $root
     * @param array $uris
     * @param array $dirs
     */
    public function __construct($root, array $uris = array(), array $dirs = array())
    {
        parent::__construct($root, $uris, $dirs);
        $this->root = str_replace('\\', '/', $root);

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

        $this->directories[\Magento\Filesystem::SYS_TMP] = array(
            'path' => sys_get_temp_dir(),
            'read_only' => false,
            'allow_create_dirs' => true,
            'permissions' => 0777
        );
    }

    /**
     * Add directory configuration
     *
     * @param string $code
     * @param array $configuration
     */
    public function addDirectory($code, array $configuration)
    {
        if (!isset($configuration['path'])) {
            $configuration['path'] = null;
        }
        $configuration['path'] = $this->makeAbsolute($configuration['path']);

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
        if ($path === null) {
            $result = '';
        } else {
            $result = $this->getRoot();
            if (!empty($path)) {
                $result .= '/' . $path;
            }
        }

        return $result;
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

    /**
     * \Directory path getter
     *
     * @param string $code One of self const
     * @return string|bool
     */
    public function getDir($code = self::ROOT)
    {
        return isset($this->directories[$code]['path']) ? $this->directories[$code]['path'] : false;
    }
}
