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
use Magento\Framework\App\Console;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Installer implements \Magento\Framework\AppInterface
{
    protected $appState;

    protected $resources;

    protected $setupFactory;

    protected $moduleList;

    protected $objectManager;

    protected $configLoader;

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

    /**
     * Launch application
     *
     * @return \Magento\Framework\App\ResponseInterface
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
}
