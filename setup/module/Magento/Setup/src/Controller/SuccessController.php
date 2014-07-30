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

class SuccessController extends AbstractActionController
{
    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $view;

    /**
     * @param ViewModel $view
     */
    public function __construct(
        ViewModel $view
    ) {
        $this->view = $view;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->view->setTerminal(true);
        return $this->view;
    }
}
