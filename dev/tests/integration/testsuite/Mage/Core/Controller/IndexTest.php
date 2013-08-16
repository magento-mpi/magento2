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

class Mage_Core_Controller_IndexTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testNotFoundAction()
    {
        $this->dispatch('core/index/notfound');
        $this->assertEquals('404', $this->getResponse()->getHttpResponseCode());
        $this->assertEquals('Requested resource not found', $this->getResponse()->getBody());
    }
}
