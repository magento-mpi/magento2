<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Install;

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
        $success = false;
        try {
            $progress = $this->progressFactory->createFromLog($this->logger);
            $percent = sprintf('%d', $progress->getRatio() * 100);
            $success = true;
            $contents = $this->logger->get();
        } catch (\Exception $e) {
            $contents = [(string)$e];
        }
        return $this->json->setVariables(['progress' => $percent, 'success' => $success, 'console' => $contents]);
    }
}
