<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Magento\Module\ModuleList;

class EnvironmentController extends AbstractActionController
{
    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $view;


    /**
     * @var \Magento\Module\ModuleList
     */
    protected $moduleList;

    /**
     * @param ViewModel $view
     * @param \Magento\Module\ModuleList $moduleList
     */
    public function __construct(
        ViewModel $view,
        ModuleList $moduleList
    ) {
        $this->view = $view;
        $this->moduleList = $moduleList;
    }

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $this->view->setTerminal(true);

        return $this->view;
    }
}
