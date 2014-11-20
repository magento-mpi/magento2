<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Magento\Setup\Model\InstallerFactory;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Magento\Setup\Model\WebLogger;

class DatabaseCheck extends AbstractActionController
{
    /**
     * Installer service factory
     *
     * @var \Magento\Setup\Model\InstallerFactory
     */
    private $installerFactory;

    /**
     * Constructor
     *
     * @param InstallerFactory $installerFactory
     */
    public function __construct(InstallerFactory $installerFactory)
    {
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
            return new JsonModel(['success' => true]);
        } catch (\Exception $e) {
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
