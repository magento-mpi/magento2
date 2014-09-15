<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Install;

use Magento\Setup\Module\ModuleListInterface;
use Magento\Setup\Model\WebLogger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ProgressController extends AbstractActionController
{
    /**
     * How many times installer will loop through the list of modules
     */
    const MODULE_LOOPS_COUNT = 2;

    /**
     * The number of additional log messages in the code
     */
    const ADDITIONAL_LOG_MESSAGE_COUNT = 15;

    /**
     * @var \Zend\View\Model\JsonModel
     */
    protected $json;

    /**
     * @var WebLogger
     */
    protected $logger;

    protected $moduleList;

    /**
     * @param JsonModel $view
     * @param ModuleListInterface $moduleList
     * @param WebLogger $logger
     */
    public function __construct(
        JsonModel $view,
        ModuleListInterface $moduleList,
        WebLogger $logger
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
        $moduleCount = count($this->moduleList->getModules());
        $log = $this->logger->get();
        $progress = 0;
        if (!empty($log)) {
            $progress = round(
                (count($log) * 100)/($moduleCount * self::MODULE_LOOPS_COUNT + self::ADDITIONAL_LOG_MESSAGE_COUNT)
            );
        }

        return $this->json->setVariables(
            array(
                'progress' => $progress,
                'success' => !$this->logger->hasError(),
                'console' => $log
            )
        );
    }
}
