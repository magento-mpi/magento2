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
use Magento\Config\ConfigFactory;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

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
     * @param JsonModel $view
     * @param ModuleListInterface $moduleList
     * @param SetupFactory $setupFactory
     * @param AdminAccountFactory $adminAccountFactory
     * @param Logger $logger
     * @param Random $random
     * @param Config $config
     */
    public function __construct(
        JsonModel $view,
        ModuleListInterface $moduleList,
        SetupFactory $setupFactory,
        AdminAccountFactory $adminAccountFactory,
        Logger $logger,
        Random $random,
        Config $config,
        ConfigFactory $systemConfig
    ) {
        $this->logger = $logger;
        $this->json = $view;
        $this->moduleList = $moduleList->getModules();
        $this->setupFactory = $setupFactory;
        $this->config = $config;
        $this->systemConfig = $systemConfig;
        $this->adminAccountFactory = $adminAccountFactory;
        $this->random = $random;
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $this->logger->clear();

        $data = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);

        $this->config->setConfigData($data);
        $this->config->install();

        $this->setupFactory->setConfig($this->config->getConfigData());

        $moduleNames = array_keys($this->moduleList);
        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyUpdates();
            $this->logger->logSuccess($moduleName);
        }

        $this->logger->logSuccess('Artifact');

        // Set data to config
        $setup->addConfigData(
            'web/seo/use_rewrites',
            isset($data['config']['rewrites']['allowed']) ? $data['config']['rewrites']['allowed'] : 0
        );
        $setup->addConfigData(
            'web/unsecure/base_url',
            isset($data['config']['address']['web']) ? $data['config']['address']['web'] : '{{unsecure_base_url}}'
        );
        $setup->addConfigData(
            'web/secure/use_in_frontend',
            isset($data['config']['https']['front']) ? $data['config']['https']['front'] : 0
        );
        $setup->addConfigData(
            'web/secure/base_url',
            isset($data['config']['address']['web']) ? $data['config']['address']['web'] : '{{secure_base_url}}'
        );
        $setup->addConfigData(
            'web/secure/use_in_adminhtml',
            isset($data['config']['https']['admin']) ? $data['config']['https']['admin'] : 0
        );
        $setup->addConfigData(
            'general/locale/code',
            isset($data['store']['language']) ? $data['store']['language'] : 'en_US'
        );
        $setup->addConfigData(
            'general/locale/timezone',
            isset($data['store']['timezone']) ? $data['store']['timezone'] : 'America/Los_Angeles'
        );

        $currencyCode = isset($data['store']['currency']) ? $data['store']['currency'] : 'USD';

        $setup->addConfigData('currency/options/base', $currencyCode);
        $setup->addConfigData('currency/options/default', $currencyCode);
        $setup->addConfigData('currency/options/allow', $currencyCode);

        // Create administrator account
        $this->adminAccountFactory->setConfig($this->config->getConfigData());
        $adminAccount = $this->adminAccountFactory->create($setup);
        $adminAccount->save();

        $this->logger->logSuccess('Admin User');

        if ($data['config']['encrypt']['type'] == 'magento') {
            $key = md5($this->random->getRandomString(10));
        } else {
            $key = $data['config']['encrypt']['key'];
        }

        $this->config->replaceTmpEncryptKey($key);
        $this->config->replaceTmpInstallDate(date('r'));
        exec('php -f '. $this->systemConfig->create()->getMagentoBasePath() . '/dev/shell/run_data_fixtures.php', $output, $exitCode);

        if($exitCode != 0) {
            $outputMsg = implode(PHP_EOL , $output);
            $this->logger->logError(new \Exception('Data Update Failed with Exit Code: ' . $exitCode . PHP_EOL . $outputMsg));
            $this->json->setVariable('success', false);
        } else {
            $this->logger->logSuccess('Data Updates');
            $this->json->setVariable('success', true);
        }

        $this->json->setVariable('key', $key);
        return $this->json;
    }

    private function execVerbose($command)
    {
        $args = func_get_args();
        $args = array_map('escapeshellarg', $args);
        $args[0] = $command;
        $command = call_user_func_array('sprintf', $args);
        echo $command . PHP_EOL;
        exec($command, $output, $exitCode);
        foreach ($output as $line) {
            echo $line . PHP_EOL;
        }
        if (0 !== $exitCode) {
            throw new Exception("Command has failed with exit code: $exitCode.");
        }
        return $output;
    }
}
