<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     toolkit_framework
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento application for performance tests
 */
namespace Magento\ToolkitFramework;

class Application
{
    /**
     * Area code
     */
    const AREA_CODE = 'install';

    /**
     * Application object
     *
     * @var \Magento\Framework\AppInterface
     */
    protected $_application;

    /**
     * @var \Magento\Framework\Shell
     */
    protected $_shell;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * List of fixtures applied to the application
     *
     * @var array
     */
    protected $_fixtures = array();

    /**
     * @var string
     */
    protected $_applicationBaseDir;

    /**
     * @param string $applicationBaseDir
     * @param \Magento\Framework\Shell $shell
     */
    public function __construct($applicationBaseDir, \Magento\Framework\Shell $shell)
    {
        $this->_applicationBaseDir = $applicationBaseDir;
        $this->_shell = $shell;
    }

    /**
     * Update permissions for `var` directory
     *
     * @return void
     */
    protected function _updateFilesystemPermissions()
    {
        /** @var \Magento\Framework\Filesystem\Directory\Write $varDirectory */
        $varDirectory = $this->getObjectManager()->get('Magento\Framework\App\Filesystem')
            ->getDirectoryWrite(\Magento\Framework\App\Filesystem::VAR_DIR);
        $varDirectory->changePermissions('', 0777);
    }

    /**
     * Bootstrap application, so it is possible to use its resources
     *
     * @return \Magento\ToolkitFramework\Application
     */
    protected function _bootstrap()
    {
        $this->getObjectManager()->configure(
            $this->getObjectManager()->get('Magento\Framework\App\ObjectManager\ConfigLoader')->load(self::AREA_CODE)
        );
        $this->getObjectManager()->get('Magento\Framework\Config\ScopeInterface')->setCurrentScope(self::AREA_CODE);
        return $this;
    }

    /**
     * Bootstrap
     *
     * @return Application
     */
    public function bootstrap()
    {
        return $this->_bootstrap();
    }

    /**
     * Run reindex
     *
     * @return Application
     */
    public function reindex()
    {
        $this->_shell->execute(
            'php -f ' . $this->_applicationBaseDir . '/dev/shell/indexer.php -- reindexall'
        );
        // TODO: remove once Magento\Index module is completely removed (MAGETWO-18168)
        $this->_shell->execute(
            'php -f ' . $this->_applicationBaseDir . '/dev/shell/newindexer.php -- reindexall'
        );
        return $this;
    }

    /**
     * Work on application, so that it has all and only $fixtures applied. May require reinstall, if
     * excessive fixtures has been applied before.
     *
     * @param array $fixtures
     *
     * @return void
     */
    public function applyFixtures(array $fixtures)
    {
        // Apply fixtures
        $fixturesToApply = array_diff($fixtures, $this->_fixtures);
        if (!$fixturesToApply) {
            return;
        }

        $this->_bootstrap();
        foreach ($fixturesToApply as $fixtureFile) {
            $this->applyFixture($fixtureFile);
        }
        $this->_fixtures = $fixtures;

        $this->reindex()
            ->_updateFilesystemPermissions();
    }

    /**
     * Apply fixture file
     *
     * @param string $fixtureFilename
     *
     * @return void
     */
    public function applyFixture($fixtureFilename)
    {
        require $fixtureFilename;
    }

    /**
     * Get object manager
     *
     * @return \Magento\Framework\ObjectManager
     */
    public function getObjectManager()
    {
        if (!$this->_objectManager) {
            $locatorFactory = new \Magento\Framework\App\ObjectManagerFactory();
            $this->_objectManager = $locatorFactory->create(BP, $_SERVER);
            $this->_objectManager->get('Magento\Framework\App\State')->setAreaCode(self::AREA_CODE);
        }
        return $this->_objectManager;
    }

    /**
     * Reset object manager
     *
     * @return \Magento\Framework\ObjectManager
     */
    public function resetObjectManager()
    {
        $this->_objectManager = null;
        return $this;
    }
}
