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

class ClearProgressController extends AbstractActionController
{
    /**
     * @param JsonModel $view
     * @param WebLogger $logger
     */
    public function __construct(
        JsonModel $view,
        WebLogger $logger
    ) {
        $this->json = $view;
        $this->logger = $logger;
    }

    /**
     * Clears installation log
     *
     * @return JsonModel
     */
    public function indexAction()
    {
        $this->logger->clear();
        $this->json->setVariable('success', true);
        return $this->json;
    }
}
