<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Controller_AjaxTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * @dataProvider translateActionDataProvider
     */
    public function testTranslateAction($postData)
    {
        $this->getRequest()->setPost('translate', $postData);
        $this->dispatch('core/ajax/translate');
        $this->assertEquals('{success:true}', $this->getResponse()->getBody());
    }

    public function translateActionDataProvider()
    {
        return array(
            array('test'),
            array(array('test'))
        );
    }
}
