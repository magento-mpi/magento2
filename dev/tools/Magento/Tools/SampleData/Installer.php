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
     * @var Helper\PostInstaller
     */
    protected $postInstaller;

    /**
     * @param State $appState
     * @param SetupFactory $setupFactory
     * @param ModuleListInterface $moduleList
     * @param ObjectManager $objectManager
     * @param ConfigLoader $configLoader
     * @param Console\Response $response
     * @param Helper\PostInstaller $postInstaller
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param array $data
     * @throws \Exception
     */
    public function __construct(
        State $appState,
        SetupFactory $setupFactory,
        ModuleListInterface $moduleList,
        ObjectManager $objectManager,
        ConfigLoader $configLoader,
        Console\Response $response,
        Helper\PostInstaller $postInstaller,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        $data = []
    ) {
        $this->appState = $appState;
        $this->setupFactory = $setupFactory;
        $this->moduleList = $moduleList;
        $this->objectManager = $objectManager;
        $this->configLoader = $configLoader;
        $this->response = $response;
        $this->postInstaller = $postInstaller;
        /** @var \Magento\User\Model\User $user */
        $user = $objectManager->create('Magento\User\Model\User')->loadByUsername($data['admin_username']);
        if (!$user || !$user->getId()) {
            throw new \Exception('Invalid username provided');
        }
        $backendAuthSession->setUser($user);
    }

    /**
     * {@inheritdoc}
     **/
    public function launch()
    {
        $areaCode = 'adminhtml';
        $this->appState->setAreaCode($areaCode);
        $this->objectManager->configure($this->configLoader->load($areaCode));

        $resources = $this->initResources();
        foreach (array_keys($this->moduleList->getModules()) as $moduleName) {
            if (isset($resources[$moduleName])) {
                $resourceType = $resources[$moduleName];
                $this->setupFactory->create($resourceType)->run();
                $this->postInstaller->addModule($moduleName);
            }
        }
        $this->postInstaller->run();

        $this->response->setCode(0);
        return $this->response;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     **/
    public function catchException(Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }

    /**
     * Init resources
     * @return array
     */
    private function initResources()
    {
        $config = [];
        foreach (glob(__DIR__ . '/config/*.php') as $filename) {
            if (is_file($filename)) {
                $configPart = include $filename;
                $config = array_merge_recursive($config, $configPart);
            }
        }
        return isset($config['setup_resources']) ? $config['setup_resources'] : [];
    }
}
