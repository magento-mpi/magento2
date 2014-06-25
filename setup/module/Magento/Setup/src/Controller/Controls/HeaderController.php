<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Controller\Controls;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class HeaderController extends AbstractActionController
{
    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $view;

    /**
     * @param ViewModel $view
     */
    public function __construct(ViewModel $view)
    {
        $this->view = $view;
        $this->view->setTemplate('/magento/setup/controls/header.phtml');
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
