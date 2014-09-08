<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Install;

use Magento\Framework\Math\Random;
use Magento\Module\ModuleListInterface;
use Magento\Module\Setup\Config;
use Magento\Module\SetupFactory;
use Magento\Setup\Model\AdminAccountFactory;
use Magento\Setup\Model\Logger;
use Magento\Setup\Model\UserConfigurationDataFactory;
use Magento\Config\ConfigFactory;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Magento\Setup\Helper\Helper;

/**
 * UI Controller that handles installation
 *
 * @package Magento\Setup\Controller\Install
 */
class StartController extends AbstractActionController
{
    /**
     * @var JsonModel
     */
    protected $json;

    /**
     * @var []
     */
    protected $moduleList;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ConfigFactory
     */
    protected $systemConfig;

    /**
     * @var AdminAccountFactory
     */
    protected $adminAccountFactory;

    /**
     * @var Random
     */
    protected $random;

    /**
     * User Configuration Data Factory
     *
     * @var UserConfigurationDataFactory
     */
    protected $userConfigurationDataFactory;

    /**
     * @param JsonModel $view
     * @param ModuleListInterface $moduleList
     * @param SetupFactory $setupFactory
     * @param AdminAccountFactory $adminAccountFactory
     * @param Logger $logger
     * @param Random $random
     * @param Config $config
     * @param ConfigFactory $systemConfig
     */
    public function __construct(
        JsonModel $view,
        ModuleListInterface $moduleList,
        SetupFactory $setupFactory,
        AdminAccountFactory $adminAccountFactory,
        Logger $logger,
        Random $random,
        Config $config,
        ConfigFactory $systemConfig,
        UserConfigurationDataFactory $userConfigurationDataFactory
    ) {
        $this->logger = $logger;
        $this->json = $view;
        $this->moduleList = $moduleList->getModules();
        $this->setupFactory = $setupFactory;
        $this->config = $config;
        $this->systemConfig = $systemConfig;
        $this->adminAccountFactory = $adminAccountFactory;
        $this->random = $random;
        $this->userConfigurationDataFactory = $userConfigurationDataFactory;
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $this->logger->clear();

        $data = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);

        $this->config->setConfigData($this->config->convertFromDataObject($data));
        $this->config->install();

        $this->setupFactory->setConfig($this->config->getConfigData());

        $moduleNames = array_keys($this->moduleList);

        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyUpdates();
            $this->logger->logSuccess($moduleName);
        }

        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyRecurringUpdates();
        }

        $this->logger->logSuccess('Artifact');

        $setup = $this->setupFactory->create('');

        // Installs Configuration Data
        $this->userConfigurationDataFactory->setConfig($this->config->getConfigData());
        $this->userConfigurationDataFactory->create($setup)->install($data);

        // Create administrator account
        $this->adminAccountFactory->setConfig($this->config->getConfigData());
        $this->adminAccountFactory->create($setup)->save();

        $this->logger->logSuccess('Admin User');

        if ($data['config']['encrypt']['type'] == 'magento') {
            $key = md5($this->random->getRandomString(10));
        } else {
            $key = $data['config']['encrypt']['key'];
        }

        $this->config->replaceTmpEncryptKey($key);
        $this->config->replaceTmpInstallDate(date('r'));

        $phpPath = Helper::phpExecutablePath();
        exec(
            $phpPath .
            'php -f ' . escapeshellarg($this->systemConfig->create()->getMagentoBasePath() .
                '/dev/shell/run_data_fixtures.php'),
            $output,
            $exitCode
        );
        if ($exitCode !== 0) {
            $outputMsg = implode(PHP_EOL, $output);
            $this->logger->logError(
                new \Exception('Data Update Failed with Exit Code: ' . $exitCode . PHP_EOL . $outputMsg)
            );
            $this->json->setVariable('success', false);
        } else {
            $this->logger->logSuccess('Data Updates');
            $this->json->setVariable('success', true);
        }

        $this->json->setVariable('key', $key);
        return $this->json;
    }
}
