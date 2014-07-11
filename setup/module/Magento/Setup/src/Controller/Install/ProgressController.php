<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller\Install;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ProgressController extends AbstractActionController
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
        return $this->json->setVariables(
            array(
                'progress' => 100,
                'success' => true,
                'console' => array(
                    'log msg 1',
                    'log msg 2',
                    'log msg 3',
                    'log msg 4',
                    'log msg 5',
                    'log msg 6',
                    'log msg 7',
                )
            )
        );
    }
}
