<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Install;

use Magento\Setup\Model\Installer;
use Magento\Setup\Model\WebLogger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Magento\Setup\Model\Installer\ProgressFactory;

class ProgressController extends AbstractActionController
{
    /**
     * JSON response
     *
     * @var \Zend\View\Model\JsonModel
     */
    protected $json;

    /**
     * Web logger
     *
     * @var WebLogger
     */
    protected $logger;

    /**
     * Progress indicator factory
     *
     * @var ProgressFactory
     */
    protected $progressFactory;

    /**
     * Constructor
     *
     * @param JsonModel $view
     * @param WebLogger $logger
     * @param ProgressFactory $progressFactory
     */
    public function __construct(
        JsonModel $view,
        WebLogger $logger,
        ProgressFactory $progressFactory
    ) {
        $this->logger = $logger;
        $this->json = $view;
        $this->progressFactory = $progressFactory;
    }

    /**
     * Checks progress of installation
     *
     * @return JsonModel
     */
    public function indexAction()
    {
        $percent = 0;
        $isError = true;
        try {
            $log = $this->logger->get();
            $isError = $this->logger->hasError();
            if (!empty($log)) {
                $progress = $this->progressFactory->createFromLog(implode('', $log), Installer::PROGRESS_LOG_REGEX);
                $percent = sprintf('%01.2F', $progress->getRatio() * 100);
            }
        } catch(\Exception $e) {
            $log = [(string)$e];
        }
        return $this->json->setVariables(['progress' => $percent, 'success' => !$isError, 'console' => $log]);
    }
}
