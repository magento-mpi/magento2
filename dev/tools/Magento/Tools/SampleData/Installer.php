<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Tools\SampleData;

use Magento\Framework\App\State;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Event;
use Magento\Framework\ObjectManager;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Console;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Installer implements \Magento\Framework\AppInterface
{
    /**
     * @var State
     */
    protected $appState;

    /**
     * @var array
     */
    protected $resources;

    /**
     * @var SetupFactory
     */
    protected $setupFactory;

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ConfigLoader
     */
    protected $configLoader;

    /**
     * @var Console\Response
     */
    protected $response;

    /**
     * @param State $appState
     * @param SetupFactory $setupFactory
     * @param ModuleListInterface $moduleList
     * @param ObjectManager $objectManager
     * @param ConfigLoader $configLoader
     * @param Console\Response $response
     * @param array $resources
     */
    public function __construct(
        State $appState,
        SetupFactory $setupFactory,
        ModuleListInterface $moduleList,
        ObjectManager $objectManager,
        ConfigLoader $configLoader,
        Console\Response $response,
        array $resources = []
    ) {
        $this->appState = $appState;
        $this->resources = $resources;
        $this->setupFactory = $setupFactory;
        $this->moduleList = $moduleList;
        $this->objectManager = $objectManager;
        $this->configLoader = $configLoader;
        $this->response = $response;
    }

    /*
     * {@inheritdoc}
     */
    public function launch()
    {
        $areaCode = 'backend';
        $this->appState->setAreaCode($areaCode);
        $this->objectManager->configure($this->configLoader->load($areaCode));

        foreach (array_keys($this->moduleList->getModules()) as $moduleName) {
            if (isset($this->resources[$moduleName])) {
                $resourceType = $this->resources[$moduleName];
                $this->setupFactory->create($resourceType)->run();
            }
        }

        $this->response->setCode(0);
        return $this->response;
    }

    /*
     * {@inheritdoc}
     */
    public function catchException(Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }
}
