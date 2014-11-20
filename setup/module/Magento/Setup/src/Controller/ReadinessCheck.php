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

class ReadinessCheck extends AbstractActionController
{
    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $view = new ViewModel;
        $view->setTerminal(true);
        return $view;
    }

    /**
     * @return array|ViewModel
     */
    public function progressAction()
    {
        $view = new ViewModel;
        $view->setTemplate('/magento/setup/readiness-check/progress.phtml');
        $view->setTerminal(true);
        return $view;
    }
}
