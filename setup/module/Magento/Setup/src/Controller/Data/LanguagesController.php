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

class LanguagesController extends AbstractActionController
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
        return $this->jsonModel->setVariable(
            'languages',
            [
                [
                    'code'  => 'en_US',
                    'title' => 'United State',
                ],
                [
                    'code'  => 'ua_UK',
                    'title' => 'Ukrainian',
                ],
            ]
        );
    }
}