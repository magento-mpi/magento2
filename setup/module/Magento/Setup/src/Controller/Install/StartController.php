<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Controller\Install;

use Zend\Mvc\Controller\AbstractActionController;
use Magento\Setup\Model\WebLogger;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;
use Magento\Setup\Model\InstallerFactory;
use Magento\Setup\Model\Installer;
use Magento\Setup\Model\UserConfigurationDataMapper as UserConfig;
use Magento\Setup\Model\AdminAccount;
use Magento\Setup\Model\DeploymentConfigMapper;

/**
 * UI Controller that handles installation
 *
 * @package Magento\Setup\Controller\Install
 */
class StartController extends AbstractActionController
{
    /**
     * JSON Model Object
     *
     * @var JsonModel
     */
    private $json;

    /**
     * @var WebLogger
     */
    private $log;

    /**
     * @var Installer
     */
    private $installer;

    /**
     * Default Constructor
     *
     * @param JsonModel $view
     * @param WebLogger $logger
     * @param InstallerFactory $installerFactory
     */
    public function __construct(
        JsonModel $view,
        WebLogger $logger,
        InstallerFactory $installerFactory
    ) {
        $this->json = $view;
        $this->log = $logger;
        $this->installer = $installerFactory->create($logger);
    }

    /**
     * Index Action
     *
     * @return JsonModel
     */
    public function indexAction()
    {
        $this->log->clear();
        try {
            $data = array_merge(
                $this->importDeploymentConfigForm(),
                $this->importUserConfigForm(),
                $this->importAdminUserForm()
            );
            $this->installer->install($data);
            $this->json->setVariable(
                'key',
                $this->installer->getInstallInfo()[
                    \Magento\Framework\App\DeploymentConfig\EncryptConfig::KEY_ENCRYPTION_KEY
                ]
            );
            $this->json->setVariable('success', true);
            $this->json->setVariable('messages', $this->installer->getInstallInfo()[Installer::INFO_MESSAGE]);
        } catch(\Exception $e) {
            $this->log->logError($e);
            $this->json->setVariable('success', false);
        }
        return $this->json;
    }

    /**
     * Maps data from request to format of deployment config model
     *
     * @return array
     */
    private function importDeploymentConfigForm()
    {
        $source = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        $result = [];
        $result[DeploymentConfigMapper::KEY_DB_HOST] = isset($source['db']['host']) ? $source['db']['host'] : '';
        $result[DeploymentConfigMapper::KEY_DB_NAME] = isset($source['db']['name']) ? $source['db']['name'] : '';
        $result[DeploymentConfigMapper::KEY_DB_USER] = isset($source['db']['user']) ? $source['db']['user'] :'';
        $result[DeploymentConfigMapper::KEY_DB_PASS] = isset($source['db']['password']) ? $source['db']['password'] : '';
        $result[DeploymentConfigMapper::KEY_DB_PREFIX] = isset($source['db']['tablePrefix']) ? $source['db']['tablePrefix'] : '';
        $result[DeploymentConfigMapper::KEY_BACKEND_FRONTNAME] = isset($source['config']['address']['admin'])
            ? $source['config']['address']['admin']
            : '';
        $result[DeploymentConfigMapper::KEY_ENCRYPTION_KEY] = isset($source['config']['encrypt']['key'])
            ? $source['config']['encrypt']['key']
            : '';
        return $result;
    }

    /**
     * Maps data from request to format of user config model
     *
     * @return array
     */
    private function importUserConfigForm()
    {
        $source = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        $result = [];
        $result[UserConfig::KEY_USE_SEF_URL] = isset($source['config']['rewrites']['allowed'])
            ? $source['config']['rewrites']['allowed'] : '';
        $result[UserConfig::KEY_BASE_URL] = isset($source['config']['address']['web'])
            ? $source['config']['address']['web'] : '';
        $result[UserConfig::KEY_IS_SECURE] = isset($source['config']['https']['front'])
            ? $source['config']['https']['front'] : '';
        $result[UserConfig::KEY_IS_SECURE_ADMIN] = isset($source['config']['https']['admin'])
            ? $source['config']['https']['admin'] : '';
        $result[UserConfig::KEY_BASE_URL_SECURE] = (isset($source['config']['https']['front'])
            || isset($source['config']['https']['admin']))
            ? $source['config']['https']['text'] : '';
        $result[UserConfig::KEY_LANGUAGE] = isset($source['store']['language'])
            ? $source['store']['language'] : '';
        $result[UserConfig::KEY_TIMEZONE] = isset($source['store']['timezone'])
            ? $source['store']['timezone'] : '';
        $result[UserConfig::KEY_CURRENCY] = isset($source['store']['currency'])
            ? $source['store']['currency'] : '';
        return $result;
    }

    /**
     * Maps data from request to format of admin account model
     *
     * @return array
     */
    private function importAdminUserForm()
    {
        $source = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        $result = [];
        $result[AdminAccount::KEY_USERNAME] = isset($source['admin']['username']) ? $source['admin']['username'] : '';
        $result[AdminAccount::KEY_PASSWORD] = isset($source['admin']['password']) ? $source['admin']['password'] : '';
        $result[AdminAccount::KEY_EMAIL] = isset($source['admin']['email']) ? $source['admin']['email'] : '';
        $result[AdminAccount::KEY_FIRST_NAME] = $result[AdminAccount::KEY_USERNAME];
        $result[AdminAccount::KEY_LAST_NAME] = $result[AdminAccount::KEY_USERNAME];
        return $result;
    }
}
