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

class IndexController extends AbstractActionController
{
    /**
     * @var ViewModel
     */
    protected $view;

    /**
     * @param ViewModel $view
     */
    public function __construct(ViewModel $view)
    {
        $this->view = $view;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        return $this->view;
    }
}
