<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework;

use Magento\Filesystem\DirectoryList;

class ObjectManagerFactory extends \Magento\App\ObjectManagerFactory
{
    /**
     * Locator class name
     *
     * @var string
     */
    protected $_locatorClassName = '\Magento\TestFramework\ObjectManager';

    /**
     * Config class name
     *
     * @var string
     */
    protected $_configClassName = '\Magento\TestFramework\ObjectManager\Config';

    /**
     * @var array
     */
    protected $_primaryConfigData = null;

    /**
     * Restore locator instance
     *
     * @param ObjectManager $objectManager
     * @param string $rootDir
     * @param array $arguments
     * @return ObjectManager
     */
    public function restore(ObjectManager $objectManager, $rootDir, array $arguments)
    {
        $directories = new DirectoryList(
            $rootDir,
            isset($arguments[DirectoryList::PARAM_APP_URIS]) ? $arguments[DirectoryList::PARAM_APP_URIS] : array(),
            isset($arguments[DirectoryList::PARAM_APP_DIRS]) ? $arguments[DirectoryList::PARAM_APP_DIRS] : array()
        );

        \Magento\TestFramework\ObjectManager::setInstance($objectManager);

        $objectManager->configure($this->_primaryConfigData);
        $objectManager->addSharedInstance($directories, 'Magento\App\Dir');
        $objectManager->addSharedInstance($directories, 'Magento\Filesystem\DirectoryList');
        $objectManager->configure(array(
            'Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy' => array(
                'parameters' => array('canSaveMap' => false)
            ),
            'default_setup' => array(
                'type' => 'Magento\TestFramework\Db\ConnectionAdapter'
            ),
            'preferences' => array(
                'Magento\Core\Model\Cookie' => 'Magento\TestFramework\Cookie',
                'Magento\App\RequestInterface' => 'Magento\TestFramework\Request',
                'Magento\App\ResponseInterface' => 'Magento\TestFramework\Response',
            ),
        ));

        $options = new \Magento\App\Config(
            $arguments,
            new \Magento\App\Config\Loader($directories)
        );

        $objectManager->addSharedInstance($options, 'Magento\App\Config');
        $objectManager->getFactory()->setArguments($options->get());
        $objectManager->configure(
            $objectManager->get('Magento\App\ObjectManager\ConfigLoader')->load('global')
        );

        /** @var \Magento\Filesystem\DirectoryList\Verification $verification */
        $verification = $objectManager->get('Magento\Filesystem\DirectoryList\Verification');
        $verification->createAndVerifyDirectories();

        return $objectManager;
    }

    /**
     * Load primary config data
     *
     * @param DirectoryList $directories
     * @param string $appMode
     * @return array
     * @throws \Magento\BootstrapException
     */
    protected function _loadPrimaryConfig(DirectoryList $directories, $appMode)
    {
        if (null === $this->_primaryConfigData) {
            $this->_primaryConfigData = parent::_loadPrimaryConfig($directories, $appMode);
        }
        return $this->_primaryConfigData;
    }
}
