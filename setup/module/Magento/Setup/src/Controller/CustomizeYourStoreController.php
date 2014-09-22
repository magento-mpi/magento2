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
use Magento\Locale\Lists;

class CustomizeYourStoreController extends AbstractActionController
{
    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $view;

    /**
     * @var \Magento\Locale\Lists
     */
    protected $list;

    /**
     * @param ViewModel $view
     * @param \Magento\Locale\Lists $list
     */
    public function __construct(
        ViewModel $view,
        Lists $list
    ) {
        $this->view = $view;
        $this->list = $list;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->view->setVariables([
            'timezone' => $this->list->getTimezoneList(),
            'currency' => $this->list->getCurrencyList(),
            'language' => $this->list->getLocaleList()
        ]);

        $this->view->setTerminal(true);
        return $this->view;
    }
}
