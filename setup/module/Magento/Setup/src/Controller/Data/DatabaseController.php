<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Setup\Controller\Data;

use Magento\Setup\Model\InstallerFactory;
use Magento\Setup\Model\WebLogger;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

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
            $installer = $this->installerFactory->create(new WebLogger());
            $password = isset($params['password']) ? $params['password'] : '';
            $installer->checkDatabaseConnection($params['name'], $params['host'], $params['user'], $password);
            return $this->jsonResponse->setVariables(['success' => true]);
        } catch (\Exception $e) {
            return $this->jsonResponse->setVariables(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
