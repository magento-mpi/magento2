<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MenuController extends AbstractActionController
{
    public function indexAction()
    {
        $items = array(
            $this->getItemObject(array(
                'name'  => 'license',
                'label' => 'License',
                'next'  => 'check-environment',
                'previous'  => null,
                'template'  => '/',
                'required'  => true,
                'validated' => false,
            )),
            $this->getItemObject(array(
                'name'  => 'check-environment',
                'label' => 'Check Environment',
                'next'  => 'access-to-database',
                'previous'  => 'license',
                'template'  => '/',
                'required'  => true,
                'validated' => false,
            )),
            $this->getItemObject(array(
                'name'  => 'access-to-database',
                'label' => 'Access to Database',
                'next'  => 'configuration-magento',
                'previous'  => 'check-environment',
                'template'  => '/',
                'required'  => true,
                'validated' => false,
            )),
            $this->getItemObject(array(
                'name'  => 'configuration-magento',
                'label' => 'Configuration Magento',
                'next'  => 'add-admin-user',
                'previous'  => 'access-to-database',
                'template'  => '/',
                'required'  => true,
                'validated' => false,
            )),
            $this->getItemObject(array(
                'name'  => 'add-admin-user',
                'label' => 'Add admin user',
                'next'  => null,
                'previous'  => 'configuration-magento',
                'template'  => '/',
                'required'  => true,
                'validated' => false,
            )),
        );

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode($items));

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/json');

        return $response;
    }

    protected function getItemObject($arguments)
    {
        $item = new \stdClass();
        foreach ($arguments as $key => $value) {
            $item->$key = $value;
        }
        return $item;
    }
}