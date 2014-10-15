<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model;

use Zend\ServiceManager\ServiceLocatorInterface;
use Magento\Config\Config;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class FileSystemFactory
{
    /**
     * Zend Framework's service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param DirectoryListFactory  $directoryListFactory
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        DirectoryListFactory  $directoryListFactory
    ) {
        $this->serviceLocator = $serviceLocator;
        $this->directoryList = $directoryListFactory->create();
    }

    /**
     * Factory method for FileSystem object
     *
     * @return FileSystem
     */
    public function create()
    {
        return new FileSystem(
            $this->directoryList,
            $this->serviceLocator->get('Magento\Framework\Filesystem\Directory\ReadFactory'),
            $this->serviceLocator->get('Magento\Framework\Filesystem\Directory\WriteFactory')
        );
    }
}
