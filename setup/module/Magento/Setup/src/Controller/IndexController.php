<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Controller;

use Magento\Setup\Model\Angular\StateProvider;
use Magento\Setup\Model\Navigation;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * @var ViewModel
     */
    protected $view;

    /**
     * @var StateProvider
     */
    protected $stateProvider;

    /**
     * @var Navigation
     */
    protected $nav;

    /**
     * @param ViewModel $view
     * @param Navigation $navigation
     * @param StateProvider $stateProvider
     */
    public function __construct(ViewModel $view, Navigation $navigation, StateProvider $stateProvider)
    {
        $this->nav = $navigation;
        $this->view = $view;
        $this->stateProvider = $stateProvider;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->stateProvider->build();
        $this->view->setVariable('nav', $this->nav->getData());
        $this->view->setVariable('stateProvider', $this->stateProvider->asJS());
        return $this->view;
    }
}
