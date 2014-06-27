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

class PhpVersionController extends AbstractActionController
{
    /**
     * The minimum required version of PHP
     */
    const PHP_VERSION_MIN = '5.4.0';

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
        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;
        if (version_compare(PHP_VERSION, self::PHP_VERSION_MIN, '<') === true) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
        }

        $data = [
            'responseType' => $responseType,
            'data' => [
                'required' => self::PHP_VERSION_MIN,
                'current' => PHP_VERSION,
            ],
        ];

        return $this->jsonModel->setVariables($data);
    }
}
