<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework;

/**
 * App class for integration test framework
 */
class App extends \Magento\Core\Model\App
{
    public function loadArea($code)
    {
        $area = $this->getArea($code);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode($code);
        $area->load();
    }
}
