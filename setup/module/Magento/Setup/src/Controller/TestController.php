<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Magento\Filesystem\Resolver;
use Zend\Mvc\Controller\AbstractActionController;

class TestController extends AbstractActionController
{
    protected $resolver;

    public function __construct(
        Resolver $resolver
    ) {
        $this->resolver = $resolver;
    }

    public function indexAction()
    {
        var_dump($this->resolver->get());die;
    }
}
