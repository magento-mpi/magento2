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
        $request = $this->getRequest();
        $params = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        $db = new DatabaseCheck($this->prepareDbConfig($params));
        var_dump($db->checkConnection());
        return $this->jsonModel->setVariables([
            'success' => true,
            'post' => $request->getPost()
        ]);
    }

    protected function prepareDbConfig(array $data = array())
    {
        return array(
            'driver'         => "Pdo",
            'dsn'            => "mysql:dbname=" . $data['name']. ";host=" .$data['host'],
            'username'       => $data['user'],
            'password'       => $data['password'],
            'driver_options' => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
            ),
        );
    }
}
