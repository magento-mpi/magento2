<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Success;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class EncryptionController extends AbstractActionController
{
    /**
     * @var \Zend\View\Model\JsonModel
     */
    protected $json;

    /**
     * @param JsonModel $view
     */
    public function __construct(
        JsonModel $view
    ) {
        $this->json = $view;
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        return $this->json->setVariables(array('key' => 'ASDASDASDASDASDASDASDASDASDASD'));
    }
}
