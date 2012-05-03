<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_IndexControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testIndexAction()
    {
        $this->dispatch('core/index/index');
        $this->assertEquals('', $this->getResponse()->getBody());
    }
}
