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

class LandingController extends AbstractActionController
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var ViewModel
     */
    protected $view;

    /**
     * @var array
     */
    protected $composerJson;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param ViewModel $view
     * @param DirectoryList $directoryList
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        ViewModel $view,
        DirectoryList $directoryList
    ) {
        $this->view = $view;
        $jsonFile = new JsonFile($directoryList->getRoot() . '/composer.json');
        $this->composerJson = $jsonFile->read();
    }

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $this->view->setTerminal(true);
        $this->view->setVariable('languages', $this->serviceLocator->get('config')['languages']);
        $this->view->setVariable('location', 'en_US');
        $this->view->setVariable('version', $this->composerJson['version']);
        return $this->view;
    }
}
