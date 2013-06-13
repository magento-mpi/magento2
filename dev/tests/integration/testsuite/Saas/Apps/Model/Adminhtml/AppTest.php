<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Apps_Model_Adminhtml_AppTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Apps_Model_Adminhtml_App
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Saas_Apps_Model_Adminhtml_App');
    }

    /**
     * @test
     */
    public function getContents()
    {
        $data = $this->_model->getContents();
        $this->markTestSkipped("The module 'Saas_Apps' turn off.");
        $this->assertContains('Magento Go extensions', $data);
    }
}