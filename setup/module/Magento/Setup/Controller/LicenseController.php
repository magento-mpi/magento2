<?php

namespace Magento\Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Magento\Setup\Model\Form\License;

class LicenseController extends AbstractActionController
{
    public function invokeAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', new License());
        $viewModel->setTerminal(true);
        return $viewModel;
    }
}
