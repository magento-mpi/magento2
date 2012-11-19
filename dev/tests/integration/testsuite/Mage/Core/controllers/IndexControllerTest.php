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
    public function testFileMissedAction()
    {
        $this->dispatch('core/index/fileMissed');
        $this->assertEquals('404', $this->getResponse()->getHttpResponseCode());
        $this->assertEquals('Requested view file not found', $this->getResponse()->getBody());
    }
}
