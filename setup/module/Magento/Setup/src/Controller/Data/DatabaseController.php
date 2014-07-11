<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Data;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Magento\Setup\Model\DatabaseCheck;

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
            $db = new DatabaseCheck($this->prepareDbConfig($params));
            return $this->jsonModel->setVariables(['success' => $db->checkConnection()]);
        } catch (\Exception $e) {
            return $this->jsonModel->setVariables(['success' => false]);
        }
    }

    protected function prepareDbConfig(array $data = array())
    {
        return array(
            'driver'         => "Pdo",
            'dsn'            => "mysql:dbname=" . $data['name']. ";host=" .$data['host'],
            'username'       => $data['user'],
            'password'       => isset($data['password']) ? $data['password'] : null,
            'driver_options' => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
            ),
        );
    }
}
