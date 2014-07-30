<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Data;

use Magento\Setup\Model\Navigation;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class StatesController extends AbstractActionController
{
    /**
     * @var Navigation
     */
    protected $navigation;

    /**
     * @var JsonModel
     */
    protected $jsonModel;

    /**
     * @param Navigation $navigation
     * @param JsonModel $jsonModel
     */
    public function __construct(Navigation $navigation, JsonModel $jsonModel)
    {
        $this->navigation = $navigation;
        $this->jsonModel = $jsonModel;
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        return $this->jsonModel->setVariable('nav', $this->navigation->getData());
    }
}