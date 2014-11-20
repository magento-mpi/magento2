<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;
use Composer\Json\JsonFile;
use Magento\Framework\App\Filesystem\DirectoryList;

class Landing extends AbstractActionController
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var array
     */
    protected $composerJson;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param DirectoryList $directoryList
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        DirectoryList $directoryList
    ) {
        $jsonFile = new JsonFile($directoryList->getRoot() . '/composer.json');
        $this->composerJson = $jsonFile->read();
    }

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setVariable('languages', $this->serviceLocator->get('config')['languages']);
        $view->setVariable('location', 'en_US');
        $view->setVariable('version', $this->composerJson['version']);
        return $view;
    }
}
