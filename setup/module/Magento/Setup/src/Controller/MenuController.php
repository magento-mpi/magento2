<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class MenuController extends AbstractActionController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $items = array(
            $this->getItemObject(array(
                'name'  => 'license',
                'label' => 'License',
                'next'  => 'environment',
                'previous'  => null,
                'template'  => '/',
                'required'  => true,
                'validated' => false,
            )),
            $this->getItemObject(array(
                'name'  => 'environment',
                'label' => 'Check Environment',
                'next'  => 'database',
                'previous'  => 'license',
                'template'  => '/',
                'required'  => true,
                'validated' => false,
            )),
            $this->getItemObject(array(
                'name'  => 'database',
                'label' => 'Access to Database',
                'next'  => 'configuration',
                'previous'  => 'environment',
                'template'  => '/',
                'required'  => true,
                'validated' => false,
            )),
            $this->getItemObject(array(
                'name'  => 'configuration',
                'label' => 'Configuration Magento',
                'next'  => 'user',
                'previous'  => 'database',
                'template'  => '/',
                'required'  => true,
                'validated' => false,
            )),
            $this->getItemObject(array(
                'name'  => 'user',
                'label' => 'Add admin user',
                'next'  => null,
                'previous'  => 'configuration',
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

    /**
     * @param array $arguments
     * @return \stdClass
     */
    protected function getItemObject($arguments)
    {
        $item = new \stdClass();
        foreach ($arguments as $key => $value) {
            $item->$key = $value;
        }
        return $item;
    }
}