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
use Magento\Setup\Model\Extensions;

class PhpExtensionsController extends AbstractActionController
{
    /**
     * @var JsonModel
     */
    protected $jsonModel;

    /**
     * @var \Magento\Setup\Model\Extensions
     */
    protected $extensions;

    /**
     * @param JsonModel $jsonModel
     * @param \Magento\Setup\Model\Extensions $extensions
     */
    public function __construct(
        JsonModel $jsonModel,
        Extensions $extensions
    ) {
        $this->jsonModel = $jsonModel;
        $this->extensions = $extensions;
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $required = $this->extensions->getRequired();
        $current = $this->extensions->getCurrent();

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
