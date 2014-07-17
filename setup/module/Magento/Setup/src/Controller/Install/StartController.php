<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Install;

use Magento\Module\ModuleListInterface;
use Magento\Module\SetupFactory;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class StartController extends AbstractActionController
{
    /**
     * @var JsonModel
     */
    protected $json;

    /**
     * @var []
     */
    protected $moduleList;

    /**
     * @param JsonModel $view
     * @param ModuleListInterface $moduleList
     * @param SetupFactory $setupFactory
     */
    public function __construct(
        JsonModel $view,
        ModuleListInterface $moduleList,
        SetupFactory $setupFactory
    ) {
        $this->json = $view;
        $this->moduleList = $moduleList->getModules();
        $this->setupFactory = $setupFactory;
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $data = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        $this->setupFactory->setConfig($data);
        $moduleNames = array_keys($this->moduleList);
        foreach ($moduleNames as $moduleName) {
            $setup = $this->setupFactory->create($moduleName);
            $setup->applyUpdates();
        }
        $this->json->setVariable('module_count', count($moduleNames));
        return $this->json;
    }
}
