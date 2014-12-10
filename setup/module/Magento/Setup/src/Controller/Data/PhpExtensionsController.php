<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Setup\Controller\Data;

use Magento\Setup\Model\PhpExtensions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class PhpExtensionsController extends AbstractActionController
{
    /**
     * @var JsonModel
     */
    protected $jsonModel;

    /**
     * @var \Magento\Setup\Model\PhpExtensions
     */
    protected $extensions;

    /**
     * @param JsonModel $jsonModel
     * @param PhpExtensions $extensions
     */
    public function __construct(
        JsonModel $jsonModel,
        PhpExtensions $extensions
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
