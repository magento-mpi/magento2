<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Magento\Setup\Model\Lists;
use Magento\Setup\Model\SampleData;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CustomizeYourStoreController extends AbstractActionController
{
    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $view;

    /**
     * @var \Magento\Setup\Model\Lists
     */
    protected $list;

    /**
     * @var SampleData
     */
    protected $sampleData;

    /**
     * @param ViewModel $view
     * @param Lists $list
     * @param SampleData $sampleData
     */
    public function __construct(
        ViewModel $view,
        Lists $list,
        SampleData $sampleData
    ) {
        $this->view = $view;
        $this->list = $list;
        $this->sampleData = $sampleData;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->view->setVariables([
            'timezone' => $this->list->getTimezoneList(),
            'currency' => $this->list->getCurrencyList(),
            'language' => $this->list->getLocaleList(),
            'isSampledataEnabled' => $this->sampleData->isDeployed(),
        ]);

        $this->view->setTerminal(true);
        return $this->view;
    }
}
