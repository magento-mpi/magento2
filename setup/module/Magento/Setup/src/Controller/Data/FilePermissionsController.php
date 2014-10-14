<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Data;

use Magento\Config\Config;
use Magento\Config\ConfigFactory;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Magento\Setup\Model\FilePermissions;

class FilePermissionsController extends AbstractActionController
{
    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var JsonModel
     */
    protected $jsonModel;

    /**
     * @var FilePermissions
     */
    protected $permissions;

    /**
     * @param JsonModel $jsonModel
     * @param FilePermissions $permissions
     * @param ConfigFactory $configFactory     *
     */
    public function __construct(
        JsonModel $jsonModel,
        FilePermissions $permissions,
        ConfigFactory $configFactory
    ) {
        $this->jsonModel = $jsonModel;
        $this->permissions = $permissions;
        $this->configFactory = $configFactory;
        $this->config = $this->configFactory->create();
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;
        if ($this->permissions->checkPermission()) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
        }

        $magentoBasePath= str_replace('\\', '/', $this->config->getMagentoBasePath()) . '/';
        $relativeRequiredPaths = [];
        foreach ($this->permissions->getRequired() as $path) {
            $relativeRequiredPaths[] = str_replace($magentoBasePath, '', $path);
        }
        $relativeCurrentPaths = [];
        foreach ($this->permissions->getCurrent() as $path) {
            $relativeCurrentPaths[] = str_replace($magentoBasePath, '', $path);
        }

        $data = [
            'responseType' => $responseType,
            'data' => [
                'required' => $relativeRequiredPaths,
                'current' => $relativeCurrentPaths,
            ],
        ];

        return $this->jsonModel->setVariables($data);
    }
}