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
use Magento\Setup\Model\Form\License;

class LicenseController extends AbstractActionController
{
    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $view;

    /**
     * @var \Magento\Setup\Model\Form\License
     */
    protected $license;

    /**
     * @param ViewModel $view
     * @param \Magento\Setup\Model\Form\License $license
     */
    public function __construct(ViewModel $view, License $license)
    {
        $this->view = $view;
        $this->license = $license;
    }

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $this->view->setVariable('form', $this->license);
        $this->view->setTerminal(true);

        return $this->view;
    }
}
