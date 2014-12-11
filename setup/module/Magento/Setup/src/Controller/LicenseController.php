<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Setup\Controller;

use Magento\Setup\Model\License;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class LicenseController
 *
 * @package Magento\Setup\Controller
 */
class LicenseController extends AbstractActionController
{
    /**
     * View object
     *
     * @var \Zend\View\Model\ViewModel
     */
    protected $view;

    /**
     * Licence Model
     *
     * @var License
     */
    protected $license;

    /**
     * Constructor
     *
     * @param License $license
     * @param ViewModel $view
     */
    public function __construct(License $license, ViewModel $view)
    {
        $this->license = $license;
        $this->view = $view;
    }

    /**
     * Displays license
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $contents = $this->license->getContents();
        if ($contents === false) {
            $this->view->setTemplate('error/404');
            $this->view->setVariable('message', 'Cannot find license file.');
        } else {
            $this->view->setVariable('license', $contents);
        }
        return $this->view;
    }
}
