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
use \Magento\Setup\Model\Location;

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
     * @var Location
     */
    protected $location;

    /**
     * @param Location $location
     * @param ServiceLocatorInterface $serviceLocator
     * @param ViewModel $view
     */
    public function __construct(Location $location, ServiceLocatorInterface $serviceLocator, ViewModel $view)
    {
        $this->location =$location;
        $this->view = $view;
    }

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $this->view->setTerminal(true);
        $this->view->setVariable('languages', $this->serviceLocator->get('config')['languages']);
        $this->view->setVariable('location', $this->location->getLocationCode());
        return $this->view;
    }
}
