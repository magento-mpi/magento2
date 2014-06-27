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

class PhpExtensionsController extends AbstractActionController
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
        // TODO:

        $required = [
            'curl',
            'dom',
            'gd',
        ];

        $current = [
            'curl',
            'dom',
        ];

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
