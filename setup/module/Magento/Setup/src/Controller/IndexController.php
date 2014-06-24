<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Controller;

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
     * @var Navigation
     */
    protected $nav;

    /**
     * @param ViewModel $view
     * @param Navigation $navigation
     */
    public function __construct(ViewModel $view, Navigation $navigation)
    {
        $this->nav = $navigation;
        $this->view = $view;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->view->setVariable('nav', $this->nav->getData());
        return $this->view;
    }
}
