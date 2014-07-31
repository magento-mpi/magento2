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
use Magento\Setup\Model\Navigation;

class MenuController extends AbstractActionController
{
    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $view;

    /**
     * @var Navigation
     */
    protected $navigation;

    /**
     * @param ViewModel $view
     * @param Navigation $navigation
     */
    public function __construct(
        ViewModel $view,
        Navigation $navigation
    ) {
        $this->view = $view;
        $this->view->setTemplate('/magento/setup/controls/menu.phtml');

        $this->navigation = $navigation;
    }

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $this->view->setVariable('menu', $this->navigation->getMenuItems());
        $this->view->setVariable('main', $this->navigation->getMainItems());
        $this->view->setTerminal(true);

        return $this->view;
    }
}
