<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Data;

use Magento\Setup\Model\InstallerFactory;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Magento\Setup\Model\WebLogger;

class DatabaseController extends AbstractActionController
{
    /**
     * JSON response object
     *
     * @var JsonModel
     */
    private $jsonResponse;

    /**
     * Installer service factory
     *
     * @var \Magento\Setup\Model\InstallerFactory
     */
    private $installerFactory;

    /**
     * Constructor
     *
     * @param JsonModel $response
     * @param InstallerFactory $installerFactory
     */
    public function __construct(JsonModel $response, InstallerFactory $installerFactory)
    {
        $this->jsonResponse = $response;
        $this->installerFactory = $installerFactory;
    }

    /**
     * Result of checking DB credentials
     *
     * @return JsonModel
     */
    public function indexAction()
    {
        $params = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        try {
            $installer = $this->installerFactory->create(new WebLogger);
            $password = isset($params['password']) ? $params['password'] : '';
            $installer->checkDatabaseConnection($params['name'], $params['host'], $params['user'], $password);
            return $this->jsonResponse->setVariables(['success' => true]);
        } catch (\Exception $e) {
            return $this->jsonResponse->setVariables(['success' => false]);
        }
    }

}
