<?php
/**
 * Console application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\App;

use Magento\App\Console\Response;

class Console implements \Magento\LauncherInterface
{
    /**
     * @var  \Magento\Install\Model\Installer\ConsoleFactory
     */
    protected $_installerFactory;

    /** @var array */
    protected $_arguments;

    /** @var \Magento\Install\App\Output */
    protected $_output;

    /**
     * @var \Magento\App\ObjectManager\ConfigLoader
     */
    protected $_loader;

    /**
     * @var \Magento\App\State
     */
    protected $_state;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Filesystem\Directory\Read
     */
    protected $rootDirectory;

    /**
     * @var \Magento\App\Console\Response
     */
    protected $_response;

    /**
     * @param \Magento\Install\Model\Installer\ConsoleFactory $installerFactory
     * @param \Magento\Install\App\Output $output
     * @param \Magento\App\State $state
     * @param \Magento\App\ObjectManager\ConfigLoader $loader
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\Filesystem $filesystem
     * @param Response $response
     * @param array $arguments
     */
    public function __construct(
        \Magento\Install\Model\Installer\ConsoleFactory $installerFactory,
        \Magento\Install\App\Output $output,
        \Magento\App\State $state,
        \Magento\App\ObjectManager\ConfigLoader $loader,
        \Magento\ObjectManager $objectManager,
        \Magento\App\Filesystem $filesystem,
        Response $response,
        array $arguments = array()
    ) {
        $this->rootDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $this->_loader = $loader;
        $this->_state  = $state;
        $this->_installerFactory = $installerFactory;
        $this->_arguments = $this->_buildInitArguments($arguments);
        $this->_output = $output;
        $this->_response = $response;
        $this->_objectManager = $objectManager;
    }

    /**
     * Customize application init arguments
     *
     * @param array $args
     * @return array
     */
    protected function _buildInitArguments(array $args)
    {
        $directories = array();
        if (!empty($args[\Magento\Install\Model\Installer\Console::OPTION_URIS])) {
            $uris = unserialize(base64_decode($args[\Magento\Install\Model\Installer\Console::OPTION_URIS]));
            foreach ($uris as $code => $uri) {
                $args[\Magento\App\Filesystem::PARAM_APP_DIRS][$code]['uri'] = $uri;
            }
        }
        if (!empty($args[\Magento\Install\Model\Installer\Console::OPTION_DIRS])) {
            $dirs = unserialize(base64_decode($args[\Magento\Install\Model\Installer\Console::OPTION_DIRS]));
            foreach ($dirs as $code => $dir) {
                $args[\Magento\App\Filesystem::PARAM_APP_DIRS][$code]['path'] = $dir;
            }
        }
        return $args;
    }

    /**
     * Install/Uninstall application
     *
     * @param \Magento\Install\Model\Installer\Console $installer
     * @return void
     */
    protected function _handleInstall(\Magento\Install\Model\Installer\Console $installer)
    {
        if (isset($this->_arguments['config'])
            && $this->rootDirectory->isExist($this->rootDirectory->getRelativePath($this->_arguments['config']))
        ) {
            $config = (array) include($this->_arguments['config']);
            $this->_arguments = array_merge((array)$config, $this->_arguments);
        }
        $isUninstallMode = isset($this->_arguments['uninstall']);
        if ($isUninstallMode) {
            $result = $installer->uninstall();
        } else {
            $result = $installer->install($this->_arguments);
        }
        if (!$installer->hasErrors()) {
            if ($isUninstallMode) {
                $msg = $result ? 'Uninstalled successfully' : 'Ignoring attempt to uninstall non-installed application';
            } else {
                $msg = 'Installed successfully' . ($result ? ' (encryption key "' . $result . '")' : '');
            }
            $this->_output->success($msg . PHP_EOL);
        } else {
            $this->_output->error(implode(PHP_EOL, $installer->getErrors()) . PHP_EOL);
        }
    }

    /**
     * Run application
     *
     * @return \Magento\App\ResponseInterface
     */
    public function launch()
    {
        $areaCode = 'install';
        $this->_state->setAreaCode($areaCode);
        $this->_objectManager->configure($this->_loader->load($areaCode));

        $installer = $this->_installerFactory->create(array('installArgs' => $this->_arguments));
        if (isset($this->_arguments['show_locales'])) {
            $this->_output->readableOutput($this->_output->prepareArray($installer->getAvailableLocales()));
        } elseif (isset($this->_arguments['show_currencies'])) {
            $this->_output->readableOutput($this->_output->prepareArray($installer->getAvailableCurrencies()));
        } elseif (isset($this->_arguments['show_timezones'])) {
            $this->_output->readableOutput($this->_output->prepareArray($installer->getAvailableTimezones()));
        } elseif (isset($this->_arguments['show_install_options'])) {
            $this->_output->readableOutput(PHP_EOL . 'Required options:');
            $this->_output->readableOutput($this->_output->alignKeys($installer->getRequiredOptions()));
            $this->_output->readableOutput(PHP_EOL . 'Non-required options:');
            $this->_output->readableOutput($this->_output->alignKeys($installer->getOtherOptions()));
            $this->_output->readableOutput(PHP_EOL . 'Flag values are considered positive if set to 1, y, true or yes.'
                . 'Any other value is considered as negative.' . PHP_EOL);
        } else {
            $this->_handleInstall($installer);
        }
        $this->_response->setCode(0);
        return $this->_response;
    }
}
