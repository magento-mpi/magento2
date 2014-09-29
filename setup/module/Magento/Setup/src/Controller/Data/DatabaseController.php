<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Data;

use Magento\Setup\Model\Installer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;

class DatabaseController extends AbstractActionController
{
    /**
     * @var JsonModel
     */
    protected $jsonModel;

    /**
     * @param JsonModel $jsonModel
     */
    public function __construct(JsonModel $jsonModel)
    {
        $this->jsonModel = $jsonModel;

    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $params = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        try {
            Installer::checkDatabaseConnection($params['name'], $params['host'], $params['user'], $params['password']);
            return $this->jsonModel->setVariables(['success' => true]);
        } catch (\Exception $e) {
            return $this->jsonModel->setVariables(['success' => false]);
        }
    }

}
