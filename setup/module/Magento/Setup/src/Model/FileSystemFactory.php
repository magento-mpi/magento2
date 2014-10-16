<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model;

use Zend\ServiceManager\ServiceLocatorInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\ReadFactory;

class FilesystemFactory
{
    /**
     * Zend Framework's service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator
    ) {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Factory method for Filesystem object
     *
     * @param ReadFactory $fileReadFactory
     * @return Filesystem
     */
    public function create(ReadFactory $fileReadFactory = null)
    {
        return new Filesystem(
            $this->serviceLocator->get('Magento\Setup\Model\DirectoryListFactory')->create(),
            $this->serviceLocator->get('Magento\Framework\Filesystem\Directory\ReadFactory'),
            $this->serviceLocator->get('Magento\Framework\Filesystem\Directory\WriteFactory'),
            $fileReadFactory
        );
    }
}
