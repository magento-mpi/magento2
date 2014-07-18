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
        Config $config
    ) {
        $this->logger = $logger;
        $this->json = $view;
        $this->moduleList = $moduleList->getModules();
        $this->setupFactory = $setupFactory;
        $this->config = $config;
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

        if (isset($data['db'])) {
            $this->setupFactory->setConfig($data['db']);
        }
        $moduleNames = array_keys($this->moduleList);
        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyUpdates();
            $this->logger->logSuccess($moduleName);
        }

        $magentoUrl = isset($data['config']['address']['web'])
            ? $data['config']['address']['web']
            : '';

        $cHandle = curl_init();
        curl_setopt($cHandle, CURLOPT_URL, $magentoUrl);
        curl_exec($cHandle);
        curl_close($cHandle);

        // Create administrator account
        $this->adminAccountFactory->setConfig($data);
        $adminAccount = $this->adminAccountFactory->create();
        $adminAccount->save();

        if ($data['config']['encrypt']['type'] == 'magento') {
            $key = md5($this->random->getRandomString(10));
        } else {
            $key = $data['config']['encrypt']['key'];
        }
        $this->config->replaceTmpEncryptKey($key);

        $this->config->replaceTmpInstallDate(date('r'));

        $this->json->setVariable('success', true);
        $this->json->setVariable('key', $key);
        return $this->json;
    }
}
