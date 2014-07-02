<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Data;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class LanguagesController extends AbstractActionController
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var JsonModel
     */
    protected $jsonModel;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param JsonModel $jsonModel
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, JsonModel $jsonModel)
    {
        $this->jsonModel = $jsonModel;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        return $this->jsonModel->setVariable('languages', $this->serviceLocator->get('config')['languages']);
    }
}