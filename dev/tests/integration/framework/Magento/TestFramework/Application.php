<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework;
use Magento\App\State,
    Magento\App\Dir;

/**
 * Encapsulates application installation, initialization and uninstall
 *
 * @todo Implement MAGETWO-1689: Standard Installation Method for Integration Tests
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
     * @var \Magento\Simplexml\Element
     */
    protected $_localXml;

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
    protected $_installDir;

    /**
     * Installation destination directory with configuration files
     *
     * @var string
     */
    protected $_installEtcDir;

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
     * Constructor
     *
     * @param \Magento\TestFramework\Db\AbstractDb $dbInstance
     * @param string $installDir
     * @param \Magento\Simplexml\Element $localXml
     * @param $globalConfigDir
     * @param array $moduleEtcFiles
     * @param string $appMode
     */
    public function __construct(
        \Magento\TestFramework\Db\AbstractDb $dbInstance, $installDir, \Magento\Simplexml\Element $localXml,
        $globalConfigDir, array $moduleEtcFiles, $appMode
    ) {
        $this->_db              = $dbInstance;
        $this->_localXml        = $localXml;
        $this->_globalConfigDir = realpath($globalConfigDir);
        $this->_moduleEtcFiles  = $moduleEtcFiles;
        $this->_appMode = $appMode;

        $this->_installDir = $installDir;
        $this->_installEtcDir = "$installDir/etc";

        $generationDir = "$installDir/generation";
        $this->_initParams = array(
            \Magento\App\Dir::PARAM_APP_DIRS => array(
                \Magento\App\Dir::CONFIG      => $this->_installEtcDir,
                \Magento\App\Dir::VAR_DIR     => $installDir,
                \Magento\App\Dir::MEDIA       => "$installDir/media",
                \Magento\App\Dir::STATIC_VIEW => "$installDir/pub_static",
                \Magento\App\Dir::PUB_VIEW_CACHE => "$installDir/pub_cache",
                \Magento\App\Dir::GENERATION => $generationDir,
            ),
            State::PARAM_MODE => $appMode
        );
    }

    /**
     * Retrieve the database adapter instance
     *
     * @return \Magento\TestFramework\Db\AbstractDb
     */
    public function getDbInstance()
    {
        return $this->_db;
    }

    /**
     * Get directory path with application instance custom data (cache, temporary directory, etc...)
     */
    public function getInstallDir()
    {
        return $this->_installDir;
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
        return is_file($this->_installEtcDir . '/local.xml');
    }

    public function initialize($overriddenParams = array())
    {
        $overriddenParams['base_dir'] = BP;
        $overriddenParams[State::PARAM_MODE] = $this->_appMode;
        $overriddenParams = $this->_customizeParams($overriddenParams);

        $directories = new Dir(
            BP,
            isset($overriddenParams[Dir::PARAM_APP_URIS]) ? $overriddenParams[Dir::PARAM_APP_URIS] : array(),
            isset($overriddenParams[Dir::PARAM_APP_DIRS]) ? $overriddenParams[Dir::PARAM_APP_DIRS] : array()
        );
        if (!\Magento\TestFramework\Helper\Bootstrap::getObjectManager()) {
            $objectManager = $this->_initNewObjectManager($overriddenParams, $directories);
        } else {
            $objectManager = $this->_restoreObjectManager(
                $overriddenParams, $directories, \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            );
        }

        \Magento\TestFramework\Helper\Bootstrap::setObjectManager($objectManager);

        /** @var \Magento\App\Dir\Verification $verification */
        $verification = $objectManager->get('Magento\App\Dir\Verification');
        $verification->createAndVerifyDirectories();

        /** @var \Magento\Core\Model\Resource $resource */
        $resource = $objectManager->get('Magento\Core\Model\Resource');
        $resource->setCache($objectManager->get('Magento\Core\Model\CacheInterface'));

        /** Register event observer of Integration Framework */
        /** @var \Magento\Event\Config\Data $eventConfigData */
        $eventConfigData = $objectManager->get('Magento\Event\Config\Data');
        $eventConfigData->merge(
            array('core_app_init_current_store_after' =>
                array('integration_tests' =>
                    array(
                        'instance' => 'Magento\TestFramework\Event\Magento',
                        'method' => 'initStoreAfter',
                        'name' => 'integration_tests'
                    )
                )
            )
        );

        $this->loadArea(\Magento\TestFramework\Application::DEFAULT_APP_AREA);
        \Magento\Phrase::setRenderer($objectManager->get('Magento\Phrase\Renderer\Placeholder'));
    }

    /**
     * Initialize Object Manager instance
     *
     * @param array $overriddenParams
     * @param Dir $directories
     * @return ObjectManager
     * @throws \Magento\BootstrapException
     */
    protected function _initNewObjectManager(array $overriddenParams, Dir $directories)
    {
        \Magento\Autoload\IncludePath::addIncludePath(array($directories->getDir(Dir::GENERATION)));

        $options = new \Magento\App\Config(
            $overriddenParams,
            new \Magento\App\Config\Loader($directories)
        );

        $autoloader = new \Magento\Autoload\IncludePath();
        $generatorIo = new \Magento\Code\Generator\Io(
            new \Magento\Io\File(), $autoloader, $directories->getDir(DIR::GENERATION)
        );
        $generator = new \Magento\Code\Generator(null, $autoloader, $generatorIo);
        $autoloader = new \Magento\Code\Generator\Autoloader($generator);
        spl_autoload_register(array($autoloader, 'load'), true, false);


        $diConfig = new \Magento\TestFramework\ObjectManager\Config();
        $appMode = $options->get(State::PARAM_MODE, State::MODE_DEFAULT);
        $primaryLoader = new \Magento\App\ObjectManager\ConfigLoader\Primary($directories, $appMode);
        try {
            $this->_primaryConfigData = $primaryLoader->load();
        } catch (\Exception $e) {
            throw new \Magento\BootstrapException($e->getMessage());
        }

        if ($this->_primaryConfigData) {
            $diConfig->extend($this->_primaryConfigData);
        }

        $factory = new \Magento\ObjectManager\Factory\Factory($diConfig, null, null, $options->get());

        $objectManager = new \Magento\TestFramework\ObjectManager($factory, $diConfig, array(
            'Magento\App\Config' => $options,
            'Magento\App\Dir' => $directories
        ));
        return $objectManager;
    }

    protected function _restoreObjectManager(
        array $overriddenParams,
        Dir $directories,
        \Magento\TestFramework\ObjectManager $objectManager
    ) {
        \Magento\TestFramework\ObjectManager::setInstance($objectManager);
        $objectManager->configure($this->_primaryConfigData);
        $objectManager->addSharedInstance($directories, 'Magento\App\Dir');
        $objectManager->configure(array(
            'Magento\Core\Model\Design\FileResolution\Strategy\Fallback\CachingProxy' => array(
                'parameters' => array('canSaveMap' => false)
            ),
            'default_setup' => array(
                'type' => 'Magento\TestFramework\Db\ConnectionAdapter'
            ),
            'preferences' => array(
                'Magento\Core\Model\Cookie' => 'Magento\TestFramework\Cookie',
                'Magento\App\RequestInterface' => 'Magento\TestFramework\Request',
                'Magento\App\ResponseInterface' => 'Magento\TestFramework\Response',
            ),
        ));

        $options = new \Magento\App\Config(
            $overriddenParams,
            new \Magento\App\Config\Loader($directories)
        );
        $objectManager->addSharedInstance($options, 'Magento\App\Config');
        $objectManager->getFactory()->setArguments($options->get());
        $objectManager->configure(
            $objectManager->get('Magento\App\ObjectManager\ConfigLoader')->load('global')
        );

        return $objectManager;
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
        $entryPoint = new \Magento\Core\Model\EntryPoint\Http(BP, array(), $objectManager);
        $entryPoint->processRequest();
    }

    /**
     * Cleanup both the database and the file system
     */
    public function cleanup()
    {
        $this->_db->cleanup();
        $this->_cleanupFilesystem();
    }

    /**
     * Install an application
     *
     * @param string $adminUserName
     * @param string $adminPassword
     * @param string $adminRoleName
     * @throws \Magento\Exception
     */
    public function install($adminUserName, $adminPassword, $adminRoleName)
    {
        $this->_ensureDirExists($this->_installDir);
        $this->_ensureDirExists($this->_installEtcDir);
        $this->_ensureDirExists($this->_installDir . DIRECTORY_SEPARATOR . 'media');
        $this->_ensureDirExists($this->_installDir . DIRECTORY_SEPARATOR . 'static');

        // Copy configuration files
        $globalConfigFiles = glob(
            $this->_globalConfigDir . DIRECTORY_SEPARATOR . '{*,*' . DIRECTORY_SEPARATOR . '*}.xml', GLOB_BRACE
        );
        foreach ($globalConfigFiles as $file) {
            $targetFile = $this->_installEtcDir . str_replace($this->_globalConfigDir, '', $file);
            $this->_ensureDirExists(dirname($targetFile));
            copy($file, $targetFile);
        }

        foreach ($this->_moduleEtcFiles as $file) {
            $targetModulesDir = $this->_installEtcDir . '/modules';
            $this->_ensureDirExists($targetModulesDir);
            copy($file, $targetModulesDir . DIRECTORY_SEPARATOR . basename($file));
        }

        /* Make sure that local.xml contains an invalid installation date */
        $installDate = (string)$this->_localXml->install->date;
        if ($installDate && strtotime($installDate)) {
            throw new \Magento\Exception('Local configuration must contain an invalid installation date.');
        }

        /* Replace local.xml */
        $targetLocalXml = $this->_installEtcDir . '/local.xml';
        $this->_localXml->asNiceXml($targetLocalXml);

        /* Initialize an application in non-installed mode */
        $this->initialize();

        /* Run all install and data-install scripts */
        /** @var $updater \Magento\App\Updater */
        $updater = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Updater');
        $updater->updateScheme();
        $updater->updateData();

        /* Enable configuration cache by default in order to improve tests performance */
        /** @var $cacheState \Magento\Core\Model\Cache\StateInterface */
        $cacheState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Cache\StateInterface');
        $cacheState->setEnabled(\Magento\Core\Model\Cache\Type\Config::TYPE_IDENTIFIER, true);
        $cacheState->setEnabled(\Magento\Core\Model\Cache\Type\Layout::TYPE_IDENTIFIER, true);
        $cacheState->setEnabled(\Magento\Core\Model\Cache\Type\Translate::TYPE_IDENTIFIER, true);
        $cacheState->setEnabled(\Magento\Eav\Model\Cache\Type::TYPE_IDENTIFIER, true);
        $cacheState->persist();

        /* Fill installation date in local.xml to indicate that application is installed */
        $localXml = file_get_contents($targetLocalXml);
        $localXml = str_replace($installDate, date('r'), $localXml, $replacementCount);
        if ($replacementCount != 1) {
            throw new \Magento\Exception("Unable to replace installation date properly in '$targetLocalXml' file.");
        }
        file_put_contents($targetLocalXml, $localXml, LOCK_EX);

        /* Add predefined admin user to the system */
        $this->_createAdminUser($adminUserName, $adminPassword, $adminRoleName);

        /* Switch an application to installed mode */
        $this->initialize();
        //hot fix for \Magento\Catalog\Model\Product\Attribute\Backend\SkuTest::testGenerateUniqueLongSku
        /** @var $appState \Magento\App\State */
        $appState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State');
        $appState->setInstallDate(date('r', strtotime('now')));
    }

    /**
     * Sub-routine for merging custom parameters with the ones defined in object state
     *
     * @param array $params
     * @return array
     */
    private function _customizeParams($params)
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

        $resource = $objectManager->get('Magento\Core\Model\Registry')
            ->registry('_singleton/Magento\Core\Model\Resource');

        \Magento\Data\Form::setElementRenderer(null);
        \Magento\Data\Form::setFieldsetRenderer(null);
        \Magento\Data\Form::setFieldsetElementRenderer(null);
        $this->_appArea = null;

        if ($resource) {
            $objectManager->get('Magento\Core\Model\Registry')
                ->register('_singleton/Magento\Core\Model\Resource', $resource);
        }
    }

    /**
     * Create a directory with write permissions or don't touch existing one
     *
     * @throws \Magento\Exception
     * @param string $dir
     */
    protected function _ensureDirExists($dir)
    {
        if (!file_exists($dir)) {
            $old = umask(0);
            mkdir($dir, 0777);
            umask($old);
        } else if (!is_dir($dir)) {
            throw new \Magento\Exception("'$dir' is not a directory.");
        }
    }

    /**
     * Remove temporary files and directories from the filesystem
     */
    protected function _cleanupFilesystem()
    {
        \Magento\Io\File::rmdirRecursive($this->_installDir);
    }

    /**
     * Creates predefined admin user to be used by tests, where admin session is required
     *
     * @param string $adminUserName
     * @param string $adminPassword
     * @param string $adminRoleName
     */
    protected function _createAdminUser($adminUserName, $adminPassword, $adminRoleName)
    {
        /** @var $user \Magento\User\Model\User */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\User\Model\User');
        $user->setData(array(
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'email'     => 'admin@example.com',
            'username'  => $adminUserName,
            'password'  => $adminPassword,
            'is_active' => 1
        ));
        $user->save();

        /** @var $roleAdmin \Magento\User\Model\Role */
        $roleAdmin = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\User\Model\Role');
        $roleAdmin->load($adminRoleName, 'role_name');

        /** @var $roleUser \Magento\User\Model\Role */
        $roleUser = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\User\Model\Role');
        $roleUser->setData(array(
            'parent_id'  => $roleAdmin->getId(),
            'tree_level' => $roleAdmin->getTreeLevel() + 1,
            'role_type'  => \Magento\User\Model\Acl\Role\User::ROLE_TYPE,
            'user_id'    => $user->getId(),
            'role_name'  => $user->getFirstname(),
        ));
        $roleUser->save();
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
        /** @var \Magento\Core\Model\App $app */

        $scope = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Config\Scope');
        $scope->setCurrentScope($areaCode);

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->configure(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\App\ObjectManager\ConfigLoader')->load($areaCode)
        );

        $app = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App');

        if ($areaCode == \Magento\TestFramework\Application::DEFAULT_APP_AREA) {
            /** @var \Magento\Config\Scope $scope */

            $app->loadAreaPart($areaCode, \Magento\Core\Model\App\Area::PART_CONFIG);
        } else {
            $app->loadArea($areaCode);
        }
    }
}
