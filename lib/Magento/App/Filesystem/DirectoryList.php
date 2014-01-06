<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Filesystem;

use Magento\App\Filesystem;

/**
 * Class DirectoryList
 * @package Magento\App\Filesystem
 */
class DirectoryList extends \Magento\Filesystem\DirectoryList
{
    /**
     * Directories configurations
     *
     * @var array
     */
    protected $directories = array(
        Filesystem::ROOT_DIR => array('path' => ''),
        Filesystem::APP_DIR => array('path' => 'app'),
        Filesystem::MODULES_DIR => array('path' => 'app/code'),
        Filesystem::THEMES_DIR => array('path' => 'app/design'),
        Filesystem::CONFIG_DIR => array('path' => 'app/etc'),
        Filesystem::LIB_DIR => array('path' => 'lib'),
        Filesystem::VAR_DIR => array('path' => 'var'),
        Filesystem::TMP_DIR => array('path' => 'var/tmp'),
        Filesystem::CACHE_DIR => array('path' => 'var/cache'),
        Filesystem::LOG_DIR => array('path' => 'var/log'),
        Filesystem::SESSION_DIR => array('path' => 'var/session'),
        Filesystem::DI_DIR => array('path' => 'var/di'),
        Filesystem::GENERATION_DIR => array('path' => 'var/generation'),
        Filesystem::HTTP => array('path' => null),
        Filesystem::PUB_DIR => array('path' => 'pub'),
        Filesystem::PUB_LIB_DIR => array('path' => 'pub/lib'),
        Filesystem::MEDIA_DIR => array('path' => 'pub/media'),
        Filesystem::UPLOAD_DIR => array('path' => 'pub/media/upload'),
        Filesystem::STATIC_VIEW_DIR => array('path' => 'pub/static'),
        Filesystem::PUB_VIEW_CACHE_DIR => array('path' => 'pub/cache'),
        Filesystem::LOCALE_DIR => array('path' => ''),
        Filesystem::SYS_TMP_DIR => array(
            'path'              => '', // @TODO fix path
            'read_only'         => false,
            'allow_create_dirs' => true,
            'permissions'       => 0777
        ));
}
