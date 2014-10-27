<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Encapsulates application installation, initialization and uninstall
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Application
{
    /**
     * Default application area
     */
    const DEFAULT_APP_AREA = 'global';

    /**
     * DB vendor adapter instance
     *
     * @var \Magento\TestFramework\Db\AbstractDb
     */
    protected $_db;

    /**
     * @var \Magento\Framework\Shell
     */
    protected $_shell;

    /**
     * @var string
     */
    private $installConfigFile;

    /**
     * @var array
     */
    protected $installConfig;

    /**
     * Application *.xml configuration files
     *
     * @var array
     */
    protected $_globalConfigDir;

    /**
     * Module declaration *.xml configuration files
     *
     * @var array
     */
    protected $_moduleEtcFiles;

    /**
     * Installation destination directory
     *
     * @var string
     */
    protected $_tmpDir;

    /**
     * Installation destination directory with configuration files
     *
     * @var string
     */
    protected $_configDir;

    /**
     * Application initialization parameters
     *
     * @var array
     */
    protected $_initParams = array();

    /**
     * Mode to run application
     *
     * @var string
     */
    protected $_appMode;

    /**
     * Application area
     *
     * @var null
     */
    protected $_appArea = null;

    /**
     * Primary DI Config
     *
     * @var array
     */
    protected $_primaryConfigData = array();

    /**
     * @var \Magento\TestFramework\ObjectManagerFactory
     */
    protected $_factory;

    /**
     * @param string $installConfigFile
     * @param string $globalConfigDir
     * @param array $moduleConfigFiles
     * @param string $appMode
     * @param string $tmpDir
     * @param \Magento\Framework\Shell $shell
     * @return Application
     */
    public static function getInstance(
        $installConfigFile,
        $globalConfigDir,
        array $moduleConfigFiles,
        $appMode,
        $tmpDir,
        \Magento\Framework\Shell $shell
    ) {
        if (!file_exists($installConfigFile)) {
            $installConfigFile = $installConfigFile . '.dist';
        }
        $sandboxUniqueId = md5(sha1_file($installConfigFile));
        $installDir = "{$tmpDir}/sandbox-{$sandboxUniqueId}";
        return new \Magento\TestFramework\Application(
            $shell,
            $installDir,
            $installConfigFile,
            $globalConfigDir,
            $moduleConfigFiles,
            $appMode
        );
    }

    /**
     * Constructor
     *
     * @param \Magento\Framework\Shell $shell
     * @param string $tmpDir
     * @param array $installConfigFile
     * @param $globalConfigDir
     * @param array $moduleEtcFiles
     * @param string $appMode
     */
    public function __construct(
        \Magento\Framework\Shell $shell,
        $tmpDir,
        $installConfigFile,
        $globalConfigDir,
        array $moduleEtcFiles,
        $appMode
    ) {
        $this->_shell = $shell;
        $this->installConfigFile = $installConfigFile;
        $this->_globalConfigDir = realpath($globalConfigDir);
        $this->_moduleEtcFiles = $moduleEtcFiles;
        $this->_appMode = $appMode;

        $this->_tmpDir = $tmpDir;

        $customDirs = $this->getCustomDirs();
        $dirList = new \Magento\Framework\App\Filesystem\DirectoryList(BP, $customDirs);

        $this->_initParams = array(
            \Magento\Framework\App\Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS => $customDirs,
            \Magento\Framework\App\State::PARAM_MODE => $appMode
        );
        $driverPool = new \Magento\Framework\Filesystem\DriverPool;
        $this->_factory = new \Magento\TestFramework\ObjectManagerFactory($dirList, $driverPool);

        $this->_configDir = $dirList->getPath(DirectoryList::CONFIG);
    }

    /**
     * Retrieve the database adapter instance
     *
     * @return \Magento\TestFramework\Db\AbstractDb
     */
    public function getDbInstance()
    {
        if (null === $this->_db) {
            if ($this->isInstalled()) {
                $localConfigFile = $this->getLocalConfig();
                $localConfig = simplexml_load_file($localConfigFile);
                $host = (string)$localConfig->connection->host;
                $user = (string)$localConfig->connection->username;
                $password = (string)$localConfig->connection->password;
                $dbName = (string)$localConfig->connection->dbName;
            } else {
                $installConfig = $this->getInstallConfig();
                $host = $installConfig['db_host'];
                $user = $installConfig['db_user'];
                $password = $installConfig['db_pass'];
                $dbName = $installConfig['db_name'];
            }
            $this->_db = new Db\Mysql(
                $host,
                $user,
                $password,
                $dbName,
                $this->getTempDir(),
                $this->_shell
            );
        }
        return $this->_db;
    }

    /**
     * @return array
     */
    protected function getInstallConfig()
    {
        if (null === $this->installConfig) {
            $this->installConfig = include $this->installConfigFile;
        }
        return $this->installConfig;
    }

    /**
     * @return string
     */
    private function getLocalConfig()
    {
        return $this->_configDir . '/local.xml';
    }

    /**
     * Get path to temporary directory
     */
    public function getTempDir()
    {
        return $this->_tmpDir;
    }

    /**
     * Retrieve application initialization parameters
     *
     * @return array
     */
    public function getInitParams()
    {
        return $this->_initParams;
    }

    /**
     * Weather the application is installed or not
     *
     * @return bool
     */
    public function isInstalled()
    {
        return is_file($this->getLocalConfig());
    }

    /**
     * Initialize application
     *
     * @param array $overriddenParams
     */
    public function initialize($overriddenParams = array())
    {
        /* @TODO implement */
        // 'db_connection_adapter' => 'Magento\TestFramework\Db\ConnectionAdapter',

        $overriddenParams[\Magento\Framework\App\State::PARAM_MODE] = $this->_appMode;
        $overriddenParams = $this->_customizeParams($overriddenParams);
        $directories = isset($overriddenParams[\Magento\Framework\App\Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS])
            ? $overriddenParams[\Magento\Framework\App\Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS]
            : array();
        $directoryList = new DirectoryList(BP, $directories);

        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Helper\Bootstrap::getObjectManager();
        if (!$objectManager) {
            $objectManager = $this->_factory->create($overriddenParams);
            $objectManager->addSharedInstance($directoryList, 'Magento\Framework\App\Filesystem\DirectoryList');
            $objectManager->addSharedInstance($directoryList, 'Magento\Framework\Filesystem\DirectoryList');
        } else {
            $objectManager = $this->_factory->restore($objectManager, $directoryList, $overriddenParams);
        }

        /** @var \Magento\TestFramework\App\Filesystem $filesystem */
        $filesystem = $objectManager->get('Magento\TestFramework\App\Filesystem');
        $objectManager->removeSharedInstance('Magento\Framework\Filesystem');
        $objectManager->addSharedInstance($filesystem, 'Magento\Framework\Filesystem');

        Helper\Bootstrap::setObjectManager($objectManager);

        $objectManager->configure(
            array(
                'preferences' => array(
                    'Magento\Framework\App\State' => 'Magento\TestFramework\App\State'
                )
            )
        );

        /** Register event observer of Integration Framework */
        /** @var \Magento\Framework\Event\Config\Data $eventConfigData */
        $eventConfigData = $objectManager->get('Magento\Framework\Event\Config\Data');
        $eventConfigData->merge(
            array(
                'core_app_init_current_store_after' => array(
                    'integration_tests' => array(
                        'instance' => 'Magento\TestFramework\Event\Magento',
                        'method' => 'initStoreAfter',
                        'name' => 'integration_tests'
                    )
                )
            )
        );

        $this->loadArea(\Magento\TestFramework\Application::DEFAULT_APP_AREA);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->configure(
            $objectManager->get('Magento\Framework\ObjectManager\DynamicConfigInterface')->getConfiguration()
        );
        \Magento\Framework\Phrase::setRenderer($objectManager->get('Magento\Framework\Phrase\RendererInterface'));
    }

    /**
     * Reset and initialize again an already installed application
     *
     * @param array $overriddenParams
     */
    public function reinitialize(array $overriddenParams = array())
    {
        $this->_resetApp();
        $this->initialize($overriddenParams);
    }

    /**
     * Run application normally, but with encapsulated initialization options
     */
    public function run()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Framework\App\Http $app */
        $app = $objectManager->get('Magento\Framework\App\Http');
        $response = $app->launch();
        $response->sendResponse();
    }

    /**
     * Cleanup both the database and the file system
     */
    public function cleanup()
    {
        $this->getDbInstance()->cleanup();
        $this->_cleanupFilesystem();
    }

    /**
     * Install an application
     *
     * @param string $adminUserName
     * @param string $adminPassword
     * @throws \Magento\Framework\Exception
     */
    public function install($adminUserName, $adminPassword)
    {
        $dirs = \Magento\Framework\App\Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS;
        $this->_ensureDirExists($this->_tmpDir);
        $this->_ensureDirExists($this->_configDir);
        $this->_ensureDirExists($this->_initParams[$dirs][DirectoryList::MEDIA][DirectoryList::PATH]);
        $this->_ensureDirExists($this->_initParams[$dirs][DirectoryList::STATIC_VIEW][DirectoryList::PATH]);
        $this->_ensureDirExists($this->_initParams[$dirs][DirectoryList::VAR_DIR][DirectoryList::PATH]);

        // Copy configuration files
        $globalConfigFiles = glob($this->_globalConfigDir . '/{*,*/*}.{xml,xml.template}', GLOB_BRACE);
        foreach ($globalConfigFiles as $file) {
            $targetFile = $this->_configDir . str_replace($this->_globalConfigDir, '', $file);
            $this->_ensureDirExists(dirname($targetFile));
            copy($file, $targetFile);
        }

        foreach ($this->_moduleEtcFiles as $file) {
            $targetModulesDir = $this->_configDir . '/modules';
            $this->_ensureDirExists($targetModulesDir);
            copy($file, $targetModulesDir . '/' . basename($file));
        }

        $installParams = $this->getInstallParams($adminUserName, $adminPassword);

        // run install script
/* @TODO determine if any of other supported parameters are needed. In particular, the database cleanup may be useful */
// [--cleanup_database]
//--base_url= --language= --timezone= --currency= --admin_username=
//--admin_password= --admin_email= --admin_firstname= --admin_lastname=
//[--admin_use_security_key=] [--key=] [--use_rewrites=]
//[--use_secure=] [--base_url_secure=] [--use_secure_admin=] [--admin_use_security_key=] [--sales_order_increment_prefix=]
        $this->_shell->execute(
            'php -f %s install ' . implode(' ', array_keys($installParams)),
            array_merge([BP . '/setup/index.php'], array_values($installParams))
        );

        // enable only specified list of caches
        /* @TODO implement this using cache enabler CLI */
//        /* Enable configuration cache by default in order to improve tests performance */
//        /** @var $cacheState \Magento\Framework\App\Cache\StateInterface */
//        $cacheState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
//            'Magento\Framework\App\Cache\StateInterface'
//        );
//        $cacheState->setEnabled(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER, true);
//        $cacheState->setEnabled(\Magento\Framework\App\Cache\Type\Layout::TYPE_IDENTIFIER, true);
//        $cacheState->setEnabled(\Magento\Framework\App\Cache\Type\Translate::TYPE_IDENTIFIER, true);
//        $cacheState->setEnabled(\Magento\Eav\Model\Cache\Type::TYPE_IDENTIFIER, true);
//        $cacheState->persist();
    }

    private function getInstallParams($adminUserName, $adminPassword)
    {
        $dirsParam = \Magento\Framework\App\Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS;
        $params = array_merge($this->getInstallConfig(), [
            'base_url' => 'http://localhost/',
            'language' => 'en_US',
            'timezone' => 'America/Chicago',
            'currency' => 'USD',
            'admin_username' => $adminUserName,
            'admin_password' => $adminPassword,
            'admin_email' => 'admin@example.com',
            'admin_firstname' => 'John',
            'admin_lastname' => 'Doe',
            $dirsParam => urldecode(http_build_query($this->_initParams[$dirsParam])),
        ]);
        $result = [];
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $result["--{$key}=%s"] = $value;
            }
        }
        return $result;
    }

    /**
     * Sub-routine for merging custom parameters with the ones defined in object state
     *
     * @param array $params
     * @return array
     */
    public function _customizeParams($params)
    {
        return array_replace_recursive($this->_initParams, $params);
    }

    /**
     * Reset application global state
     */
    protected function _resetApp()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->clearCache();

        \Magento\Framework\Data\Form::setElementRenderer(null);
        \Magento\Framework\Data\Form::setFieldsetRenderer(null);
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(null);
        $this->_appArea = null;
    }

    /**
     * Create a directory with write permissions or don't touch existing one
     *
     * @throws \Magento\Framework\Exception
     * @param string $dir
     */
    protected function _ensureDirExists($dir)
    {
        if (!file_exists($dir)) {
            $old = umask(0);
            mkdir($dir, 0777);
            umask($old);
        } elseif (!is_dir($dir)) {
            throw new \Magento\Framework\Exception("'$dir' is not a directory.");
        }
    }

    /**
     * Remove temporary files and directories from the filesystem
     */
    protected function _cleanupFilesystem()
    {
        if (!is_dir($this->_tmpDir)) {
            return;
        }
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->_tmpDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $path) {
            $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
        }
        rmdir($this->_tmpDir);
    }

    /**
     * Ge current application area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_appArea;
    }

    /**
     * Load application area
     *
     * @param $areaCode
     */
    public function loadArea($areaCode)
    {
        $this->_appArea = $areaCode;
        $scope = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Config\Scope');
        $scope->setCurrentScope($areaCode);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->configure(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Framework\App\ObjectManager\ConfigLoader'
            )->load(
                $areaCode
            )
        );
        $app = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\AreaList');
        if ($areaCode == \Magento\TestFramework\Application::DEFAULT_APP_AREA) {
            $app->getArea($areaCode)->load(\Magento\Framework\App\Area::PART_CONFIG);
        } else {
            \Magento\TestFramework\Helper\Bootstrap::getInstance()->loadArea($areaCode);
        }
    }

    /**
     * @return array
     */
    protected function getCustomDirs()
    {
        $generationDir = "{$this->_tmpDir}/generation";
        $customDirs = array(
            DirectoryList::CONFIG => array(DirectoryList::PATH => "{$this->_tmpDir}/etc"),
            DirectoryList::VAR_DIR => array(DirectoryList::PATH => $this->_tmpDir),
            DirectoryList::MEDIA => array(DirectoryList::PATH => "{$this->_tmpDir}/media"),
            DirectoryList::STATIC_VIEW => array(DirectoryList::PATH => "{$this->_tmpDir}/pub_static"),
            DirectoryList::GENERATION => array(DirectoryList::PATH => $generationDir),
            DirectoryList::CACHE => array(DirectoryList::PATH => $this->_tmpDir . '/cache'),
            DirectoryList::LOG => array(DirectoryList::PATH => $this->_tmpDir . '/log'),
            DirectoryList::THEMES => array(DirectoryList::PATH => BP . '/app/design'),
            DirectoryList::SESSION => array(DirectoryList::PATH => $this->_tmpDir . '/session'),
            DirectoryList::TMP => array(DirectoryList::PATH => $this->_tmpDir . '/tmp'),
            DirectoryList::UPLOAD => array(DirectoryList::PATH => $this->_tmpDir . '/upload'),
        );
        return $customDirs;
    }
}
