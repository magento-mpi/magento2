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
use Magento\Setup\Model\FilePermissions;

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
        $required = $this->permissions->getRequired();
        $current = $this->permissions->getCurrent();

        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;
        if (array_diff($required, $current)) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
        }

        $data = [
            'responseType' => $responseType,
            'data' => [
                'required' => $required,
                'current' => $current,
            ],
        ];

        return $this->jsonModel->setVariables($data);
    }
}
