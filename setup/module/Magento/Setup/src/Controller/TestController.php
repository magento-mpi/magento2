<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Magento\Config\Resolver;
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
        $content = file_get_contents($this->resolver->get()[0]);
        var_dump($content);
        $xml = new \Magento\Config\Dom($content);
        var_dump($xml->getDom()->getElementsByTagName('create')->item(0)->attributes->item(0)->nodeValue);
        die;
    }
}
