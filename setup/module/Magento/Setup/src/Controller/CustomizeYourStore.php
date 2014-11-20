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
use Magento\Setup\Model\Lists;

class CustomizeYourStore extends AbstractActionController
{
    /**
     * @var Lists
     */
    protected $list;

    /**
     * @param Lists $list
     */
    public function __construct(Lists $list)
    {
        $this->list = $list;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $view = new ViewModel([
            'timezone' => $this->list->getTimezoneList(),
            'currency' => $this->list->getCurrencyList(),
            'language' => $this->list->getLocaleList()
        ]);
        $view->setTerminal(true);
        return $view;
    }
}
