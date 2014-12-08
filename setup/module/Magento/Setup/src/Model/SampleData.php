<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;

/**
 * Sample data installer
 *
 * Serves as an integration point between Magento Setup application and Luma sample data component
 */
class SampleData
{
    /**
     * Path to the sample data application
     */
    const PATH = 'dev/tools/Magento/Tools/SampleData';

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $rootDir;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->rootDir = $filesystem->getDirectoryRead(DirectoryList::ROOT);
    }

    /**
     * Check if Sample Data was deployed
     *
     * @return bool
     */
    public function isDeployed()
    {
        return $this->rootDir->isExist(self::PATH);
    }

    /**
     * Installation routine for creating sample data
     *
     * @param ObjectManagerInterface $objectManager
     * @param LoggerInterface $logger
     * @param $adminUserName
     * @throws \Exception
     */
    public function install(ObjectManagerInterface $objectManager, LoggerInterface $logger, $adminUserName)
    {
        /** @var \Magento\Tools\SampleData\Logger $sampleDataLogger */
        $sampleDataLogger = $objectManager->get('Magento\Tools\SampleData\Logger');
        $sampleDataLogger->setSubject($logger);

        /** @var \Magento\User\Model\UserFactory $userFactory */
        $userFactory = $objectManager->get('Magento\User\Model\UserFactory');
        $user = $userFactory->create()->loadByUsername($adminUserName);
        if (!$user || !$user->getId()) {
            throw new \Exception('Invalid username provided');
        }
        /** @var \Magento\Backend\Model\Auth\Session $session */
        $session = $objectManager->get('Magento\Backend\Model\Auth\Session');
        $session->setUser($user);

        $areaCode = 'adminhtml';
        /** @var \Magento\Framework\App\State $appState */
        $appState = $objectManager->get('Magento\Framework\App\State');
        $appState->setAreaCode($areaCode);
        /** @var \Magento\Framework\App\ObjectManager\ConfigLoader $configLoader */
        $configLoader = $objectManager->get('Magento\Framework\App\ObjectManager\ConfigLoader');
        $objectManager->configure($configLoader->load($areaCode));

        /** @var \Magento\Tools\SampleData\Helper\Deploy $helper */
        $helper = $objectManager->get('Magento\Tools\SampleData\Helper\Deploy');
        $helper->run();

        /** @var \Magento\Framework\Module\ModuleListInterface $moduleList */
        $moduleList = $objectManager->get('Magento\Framework\Module\ModuleListInterface');

        /** @var \Magento\Tools\SampleData\SetupFactory $setupFactory */
        $setupFactory = $objectManager->get('Magento\Tools\SampleData\SetupFactory');

        /** @var \Magento\Tools\SampleData\Helper\PostInstaller $postInstaller */
        $postInstaller = $objectManager->get('Magento\Tools\SampleData\Helper\PostInstaller');

        $resources = $this->getSetupResourceModels();
        foreach ($moduleList->getNames() as $moduleName) {
            if (isset($resources[$moduleName])) {
                $class = $resources[$moduleName];
                $setupFactory->create($class)->run();
                $postInstaller->addModule($moduleName);
            }
        }

        $session->unsUser();

        /** @var \Magento\Tools\SampleData\Helper\PostInstaller $postInstaller */
        $postInstaller = $objectManager->get('Magento\Tools\SampleData\Helper\PostInstaller');
        $postInstaller->run();
    }

    /**
     * Determines which resource models to run during setup
     *
     * @return array
     */
    private function getSetupResourceModels()
    {
        $config = [];
        foreach ($this->rootDir->search(self::PATH . '/config/*.php') as $filename) {
            if ($this->rootDir->isFile($filename)) {
                $configPart = include $this->rootDir->getAbsolutePath($filename);
                $config = array_merge_recursive($config, $configPart);
            }
        }
        return isset($config['setup_resources']) ? $config['setup_resources'] : [];
    }
}
