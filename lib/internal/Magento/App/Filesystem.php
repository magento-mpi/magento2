<?php
/**
 * Magento application filesystem facade
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class Filesystem extends \Magento\Filesystem
{
    /**
     * Custom application dirs
     */
    const PARAM_APP_DIRS = 'app_dirs';

    /**
     * Code base root
     */
    const ROOT_DIR = 'base';

    /**
     * Most of entire application
     */
    const APP_DIR = 'app';

    /**
     * Modules
     */
    const MODULES_DIR = 'code';

    /**
     * Themes
     */
    const THEMES_DIR = 'design';

    /**
     * Initial configuration of the application
     */
    const CONFIG_DIR = 'etc';

    /**
     * Libraries or third-party components
     */
    const LIB_INTERNAL = 'lib_internal';

    /**
     * Libraries/components that need to be accessible publicly through web-server (such as various DHTML components)
     */
    const LIB_WEB = 'lib_web';

    /**
     * Files with translation of system labels and messages from en_US to other languages
     */
    const LOCALE_DIR = 'i18n';

    /**
     * \Directory within document root of a web-server to access static view files publicly
     */
    const PUB_DIR = 'pub';

    /**
     * Storage of files entered or generated by the end-user
     */
    const MEDIA_DIR = 'media';

    /**
     * Storage of static view files that are needed on HTML-pages, emails or similar content
     */
    const STATIC_VIEW_DIR = 'static';

    /**
     * Various files generated by the system in runtime
     */
    const VAR_DIR = 'var';

    /**
     * Temporary files
     */
    const TMP_DIR = 'tmp';

    /**
     * File system caching directory (if file system caching is used)
     */
    const CACHE_DIR = 'cache';

    /**
     * Logs of system messages and errors
     */
    const LOG_DIR = 'log';

    /**
     * File system session directory (if file system session storage is used)
     */
    const SESSION_DIR = 'session';

    /**
     * Dependency injection related file directory
     *
     */
    const DI_DIR = 'di';

    /**
     * Relative directory key for generated code
     */
    const GENERATION_DIR = 'generation';

    /**
     * Temporary directory for uploading files by end-user
     */
    const UPLOAD_DIR = 'upload';

    /**
     * System base temporary folder
     */
    const SYS_TMP_DIR = 'sys_tmp';

    /**
     * Retrieve absolute path for for given code
     *
     * @param string $code
     * @return string
     */
    public function getPath($code = self::ROOT_DIR)
    {
        $config = $this->directoryList->getConfig($code);
        $path = isset($config['path']) ? $config['path'] : '';
        return str_replace('\\', '/', $path);
    }
}
