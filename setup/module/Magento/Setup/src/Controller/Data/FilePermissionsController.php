<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Setup\Controller\Data;

use Magento\Setup\Model\FilePermissions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class FilePermissionsController extends AbstractActionController
{
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
     */
    public function __construct(
        JsonModel $jsonModel,
        FilePermissions $permissions
    ) {
        $this->jsonModel = $jsonModel;
        $this->permissions = $permissions;
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;
        if ($this->permissions->getMissingWritableDirectoriesForInstallation()) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
        }

        $data = [
            'responseType' => $responseType,
            'data' => [
                'required' => $this->permissions->getInstallationWritableDirectories(),
                'current' => $this->permissions->getInstallationCurrentWritableDirectories(),
            ],
        ];

        return $this->jsonModel->setVariables($data);
    }
}
