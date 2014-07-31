<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Install;

use Magento\Module\ModuleListInterface;
use Magento\Setup\Model\Logger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ProgressController extends AbstractActionController
{
    /**
     * @var \Zend\View\Model\JsonModel
     */
    protected $json;

    /**
     * @var Logger
     */
    protected $logger;

    protected $moduleList;

    /**
     * @param JsonModel $view
     * @param ModuleListInterface $moduleList
     * @param Logger $logger
     */
    public function __construct(
        JsonModel $view,
        ModuleListInterface $moduleList,
        Logger $logger
    ) {
        $this->moduleList = $moduleList;
        $this->logger = $logger;
        $this->json = $view;
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        //@todo I fix it
        $moduleCount = count($this->moduleList->getModules());
        $log = $this->logger->get();
        $progress = 0;
        if (!empty($log)) {
            $progress = round(count($log)/$moduleCount*90);
        }
        $progress += 5;

        return $this->json->setVariables(
            array(
                'progress' => $progress,
                'success' => !$this->logger->hasError(),
                'console' => $log
            )
        );
    }
}
