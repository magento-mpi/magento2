<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Install;

use Magento\Module\ModuleListInterface;
use Magento\Module\Setup\Config;
use Magento\Module\SetupFactory;
use Magento\Setup\Controller\AbstractInstallActionController;
use Magento\Setup\Model\AdminAccountFactory;
use Magento\Setup\Model\WebLogger;
use Magento\Setup\Model\UserConfigurationDataFactory;
use Magento\Config\ConfigFactory;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * UI Controller that handles installation
 *
 * @package Magento\Setup\Controller\Install
 */
class StartController extends AbstractInstallActionController
{
    /**
     * JSON Model Object
     *
     * @var JsonModel
     */
    protected $json;

    /**
     * Default Constructor
     *
     * @param ModuleListInterface $moduleList
     * @param SetupFactory $setupFactory
     * @param AdminAccountFactory $adminAccountFactory
     * @param Config $config
     * @param ConfigFactory $systemConfig
     * @param UserConfigurationDataFactory $userConfigurationDataFactory
     * @param JsonModel $view
     * @param WebLogger $logger
     * @param PhpExecutableFinder $phpExecutableFinder
     */
    public function __construct(
        ModuleListInterface $moduleList,
        SetupFactory $setupFactory,
        AdminAccountFactory $adminAccountFactory,
        Config $config,
        ConfigFactory $systemConfig,
        UserConfigurationDataFactory $userConfigurationDataFactory,
        JsonModel $view,
        WebLogger $logger,
        PhpExecutableFinder $phpExecutableFinder
    ) {
        parent::__construct(
            $moduleList,
            $setupFactory,
            $adminAccountFactory,
            $config,
            $systemConfig,
            $userConfigurationDataFactory,
            $logger,
            $phpExecutableFinder
        );
        $this->json = $view;
    }

    /**
     * Index Action
     *
     * @return JsonModel
     */
    public function indexAction()
    {
        $key = '';
        try {
            $data = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);

            //Installs Deployment Configuration
            $key = $this->installDeploymentConfiguration($data);

            //Installs Schema
            $this->installSchemaUpdates();

            // Installs User Configuration Data
            $this->installUserConfigurationData($data);

            //Installs Data
            $this->installDataUpdates();
            $this->json->setVariable('success', true);
        } catch(\Exception $ex) {
            $this->logger->logError($ex);
            $this->json->setVariable('success', false);
        }

        $this->json->setVariable('key', $key);
        return $this->json;
    }
}
