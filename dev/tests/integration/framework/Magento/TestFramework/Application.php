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

/**
 * Encapsulates application installation, initialization and uninstall
 *
 * @todo Implement MAGETWO-1689: Standard Installation Method for Integration Tests
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_TestFramework_Application
{
    /**
     * Default application area
     */
    const DEFAULT_APP_AREA = 'global';

    /**
     * DB vendor adapter instance
     *
     * @var Magento_TestFramework_Db_DbAbstract
     */
    protected $_db;

    /**
     * @var Magento_Simplexml_Element
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
    protected $_primaryConfig = array();

    /**
     * Constructor
     *
     * @param Magento_TestFramework_Db_DbAbstract $dbInstance
     * @param string $installDir
     * @param Magento_Simplexml_Element $localXml
     * @param $globalConfigDir
     * @param array $moduleEtcFiles
     * @param string $appMode
     */
    public function __construct(
        Magento_TestFramework_Db_DbAbstract $dbInstance, $installDir, Magento_Simplexml_Element $localXml,
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
            Mage::PARAM_APP_DIRS => array(
                Magento_Core_Model_Dir::CONFIG      => $this->_installEtcDir,
                Magento_Core_Model_Dir::VAR_DIR     => $installDir,
                Magento_Core_Model_Dir::MEDIA       => "$installDir/media",
                Magento_Core_Model_Dir::STATIC_VIEW => "$installDir/pub_static",
                Magento_Core_Model_Dir::PUB_VIEW_CACHE => "$installDir/pub_cache",
                Magento_Core_Model_Dir::GENERATION => $generationDir,
            ),
            Mage::PARAM_MODE => $appMode
        );
    }

    /**
     * Retrieve the database adapter instance
     *
     * @return Magento_TestFramework_Db_DbAbstract
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

    /**
     * Initialize an already installed application
     *
     * @param array $overriddenParams
     */
    public function initialize($overriddenParams = array())
    {
        $overriddenParams[Mage::PARAM_BASEDIR] = BP;
        $overriddenParams[Mage::PARAM_MODE] = $this->_appMode;
        Mage::$headersSentThrowsException = false;
        $config = new Magento_Core_Model_Config_Primary(BP, $this->_customizeParams($overriddenParams));
        if (!Magento_TestFramework_Helper_Bootstrap::getObjectManager()) {
            $objectManager = new Magento_TestFramework_ObjectManager($config,
                new Magento_TestFramework_ObjectManager_Config());
            $primaryLoader = new Magento_Core_Model_ObjectManager_ConfigLoader_Primary($config->getDirectories());
            $this->_primaryConfig = $primaryLoader->load();
            $objectManager->get('Magento_Core_Model_Resource')
                ->setResourceConfig(Mage::getObjectManager()->get('Magento_Core_Model_Config_Resource'));
        } else {
            $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
            $config->configure($objectManager);
            $objectManager->addSharedInstance($config, 'Magento_Core_Model_Config_Primary');
            $objectManager->addSharedInstance($config->getDirectories(), 'Magento_Core_Model_Dir');
            $objectManager->loadPrimaryConfig($this->_primaryConfig);
            /** @var $configResource Magento_Core_Model_Config_Resource */
            $configResource = $objectManager->get('Magento_Core_Model_Config_Resource');
            $configResource->setConfig($config);
            $objectManager->get('Magento_Core_Model_Resource')->setResourceConfig($configResource);
            $verification = $objectManager->get('Magento_Core_Model_Dir_Verification');
            $verification->createAndVerifyDirectories();
            $objectManager->configure(
                $objectManager->get('Magento_Core_Model_ObjectManager_ConfigLoader')->load('global')
            );
        }
        Magento_TestFramework_Helper_Bootstrap::setObjectManager($objectManager);
        $objectManager->get('Magento_Core_Model_Resource')
            ->setResourceConfig($objectManager->get('Magento_Core_Model_Config_Resource'));
        $objectManager->get('Magento_Core_Model_Resource')
            ->setCache($objectManager->get('Magento_Core_Model_CacheInterface'));

        /** Register event observer of Integration Framework */
        /** @var Magento_Core_Model_Event_Config_Data $eventConfigData */
        $eventConfigData = $objectManager->get('Magento_Core_Model_Event_Config_Data');
        $eventConfigData->merge(
            array('core_app_init_current_store_after' =>
                array('integration_tests' =>
                    array(
                        'instance' => 'Magento_TestFramework_Event_Magento',
                        'method' => 'initStoreAfter',
                        'name' => 'integration_tests'
                    )
                )
            )
        );
        /** @var Magento_Core_Model_Dir_Verification $verification */
        $verification = $objectManager->get('Magento_Core_Model_Dir_Verification');
        $verification->createAndVerifyDirectories();

        $this->loadArea(Magento_TestFramework_Application::DEFAULT_APP_AREA);

        Magento_Phrase::setRenderer($objectManager->get('Magento_Phrase_Renderer_Placeholder'));
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
     *
     * @param Magento_TestFramework_Request $request
     * @param Magento_TestFramework_Response $response
     */
    public function run(Magento_TestFramework_Request $request, Magento_TestFramework_Response $response)
    {
        $composer = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $handler = $composer->get('Magento_HTTP_Handler_Composite');
        $handler->handle($request, $response);
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
     * @throws Magento_Exception
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
        $installDate = (string)$this->_localXml->global->install->date;
        if ($installDate && strtotime($installDate)) {
            throw new Magento_Exception('Local configuration must contain an invalid installation date.');
        }

        /* Replace local.xml */
        $targetLocalXml = $this->_installEtcDir . '/local.xml';
        $this->_localXml->asNiceXml($targetLocalXml);

        /* Initialize an application in non-installed mode */
        $this->initialize();

        /* Run all install and data-install scripts */
        /** @var $updater Magento_Core_Model_Db_Updater */
        $updater = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Db_Updater');
        $updater->updateScheme();
        $updater->updateData();

        /* Enable configuration cache by default in order to improve tests performance */
        /** @var $cacheState Magento_Core_Model_Cache_StateInterface */
        $cacheState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Cache_StateInterface');
        $cacheState->setEnabled(Magento_Core_Model_Cache_Type_Config::TYPE_IDENTIFIER, true);
        $cacheState->setEnabled(Magento_Core_Model_Cache_Type_Layout::TYPE_IDENTIFIER, true);
        $cacheState->setEnabled(Magento_Core_Model_Cache_Type_Translate::TYPE_IDENTIFIER, true);
        $cacheState->setEnabled(Magento_Eav_Model_Cache_Type::TYPE_IDENTIFIER, true);
        $cacheState->persist();

        /* Fill installation date in local.xml to indicate that application is installed */
        $localXml = file_get_contents($targetLocalXml);
        $localXml = str_replace($installDate, date('r'), $localXml, $replacementCount);
        if ($replacementCount != 1) {
            throw new Magento_Exception("Unable to replace installation date properly in '$targetLocalXml' file.");
        }
        file_put_contents($targetLocalXml, $localXml, LOCK_EX);

        /* Add predefined admin user to the system */
        $this->_createAdminUser($adminUserName, $adminPassword, $adminRoleName);

        /* Switch an application to installed mode */
        $this->initialize();
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
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->clearCache();

        $resource = Mage::registry('_singleton/Magento_Core_Model_Resource');

        Mage::reset();
        Mage::setObjectManager($objectManager);
        Magento_Data_Form::setElementRenderer(null);
        Magento_Data_Form::setFieldsetRenderer(null);
        Magento_Data_Form::setFieldsetElementRenderer(null);
        $this->_appArea = null;

        if ($resource) {
            Mage::register('_singleton/Magento_Core_Model_Resource', $resource);
        }
    }

    /**
     * Create a directory with write permissions or don't touch existing one
     *
     * @throws Magento_Exception
     * @param string $dir
     */
    protected function _ensureDirExists($dir)
    {
        if (!file_exists($dir)) {
            $old = umask(0);
            mkdir($dir, 0777);
            umask($old);
        } else if (!is_dir($dir)) {
            throw new Magento_Exception("'$dir' is not a directory.");
        }
    }

    /**
     * Remove temporary files and directories from the filesystem
     */
    protected function _cleanupFilesystem()
    {
        Magento_Io_File::rmdirRecursive($this->_installDir);
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
        /** @var $user Magento_User_Model_User */
        $user = mage::getModel('Magento_User_Model_User');
        $user->setData(array(
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'email'     => 'admin@example.com',
            'username'  => $adminUserName,
            'password'  => $adminPassword,
            'is_active' => 1
        ));
        $user->save();

        /** @var $roleAdmin Magento_User_Model_Role */
        $roleAdmin = Mage::getModel('Magento_User_Model_Role');
        $roleAdmin->load($adminRoleName, 'role_name');

        /** @var $roleUser Magento_User_Model_Role */
        $roleUser = Mage::getModel('Magento_User_Model_Role');
        $roleUser->setData(array(
            'parent_id'  => $roleAdmin->getId(),
            'tree_level' => $roleAdmin->getTreeLevel() + 1,
            'role_type'  => Magento_User_Model_Acl_Role_User::ROLE_TYPE,
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
     * @param $area
     */
    public function loadArea($area)
    {
        $this->_appArea = $area;
        if ($area == Magento_TestFramework_Application::DEFAULT_APP_AREA) {
            Mage::app()->loadAreaPart($area, Magento_Core_Model_App_Area::PART_CONFIG);
        } else {
            Mage::app()->loadArea($area);
        }
    }
}
