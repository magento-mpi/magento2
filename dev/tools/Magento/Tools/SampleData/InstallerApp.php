<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\SampleData;

use Magento\Framework\App\State;
use Magento\Framework\Event;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Console;

/**
 * Sample data installation application
 */
class InstallerApp implements \Magento\Framework\AppInterface
{
    /**
     * @var State
     */
    private $appState;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ConfigLoader
     */
    private $configLoader;

    /**
     * @var Console\Response
     */
    private $response;

    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    private $userFactory;

    /**
     * @var string
     */
    private $adminUserName;

    /**
     * Construct
     *
     * @param State $appState
     * @param Installer $installer
     * @param ObjectManagerInterface $objectManager
     * @param ConfigLoader $configLoader
     * @param Console\Response $response
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param array $data
     */
    public function __construct(
        State $appState,
        Installer $installer,
        ObjectManagerInterface $objectManager,
        ConfigLoader $configLoader,
        Console\Response $response,
        \Magento\User\Model\UserFactory $userFactory,
        array $data = []
    ) {
        $this->appState = $appState;
        $this->objectManager = $objectManager;
        $this->configLoader = $configLoader;
        $this->response = $response;
        $this->installer = $installer;
        $this->userFactory = $userFactory;
        $this->adminUserName = isset($data['admin_username']) ? $data['admin_username'] : '';
    }

    /**
     * {@inheritdoc}
     **/
    public function launch()
    {
        $areaCode = 'adminhtml';
        $this->appState->setAreaCode($areaCode);
        $this->objectManager->configure($this->configLoader->load($areaCode));
        /** @var \Magento\Tools\SampleData\Logger $sampleDataLogger */
        $sampleDataLogger = $this->objectManager->get('Magento\Tools\SampleData\Logger');
        $sampleDataLogger->setSubject($this->objectManager->get('Magento\Setup\Model\ConsoleLogger'));

        $this->installer->run($this->userFactory->create()->loadByUsername($this->adminUserName));

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
}
