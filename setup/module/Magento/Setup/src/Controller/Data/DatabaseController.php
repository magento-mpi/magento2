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
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Magento\Setup\Model\WebLogger;

class DatabaseController extends AbstractActionController
{
    /**
     * @var JsonModel
     */
    protected $jsonModel;

    /**
     * @var \Magento\Setup\Model\InstallerFactory
     */
    protected $installerFactory;

    /**
     * @param JsonModel $jsonModel
     * @param InstallerFactory $installerFactory
     */
    public function __construct(JsonModel $jsonModel, InstallerFactory $installerFactory)
    {
        $this->jsonModel = $jsonModel;
        $this->installerFactory = $installerFactory;
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $params = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        try {
            $installer = $this->installerFactory->create(new WebLogger);
            $installer->checkDatabaseConnection($params['name'], $params['host'], $params['user'], $params['password']);
            return $this->jsonModel->setVariables(['success' => true]);
        } catch (\Exception $e) {
            return $this->jsonModel->setVariables(['success' => false]);
        }
    }

}
