<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Magento\Setup\Model\SampleData;
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
     * @var SampleData
     */
    protected $sampleData;

    /**
     * @param Lists $list
     * @param SampleData $sampleData
     */
    public function __construct(Lists $list, SampleData $sampleData)
    {
        $this->list = $list;
        $this->sampleData = $sampleData;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $view = new ViewModel([
            'timezone' => $this->list->getTimezoneList(),
            'currency' => $this->list->getCurrencyList(),
            'language' => $this->list->getLocaleList(),
            'isSampledataEnabled' => $this->sampleData->isDeployed()
        ]);
        $view->setTerminal(true);
        return $view;
    }
}
