<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

/**
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Core_AjaxTest extends Mage_Selenium_TestCase
{
    /**
     * @dataProvider translateActionDataProvider
     * @test
     */
    public function testTranslateAction($postData)
    {
        $this->coreHelper()->post('ajax_translate', array('translate' => $postData));
        $this->assertContains('{success:true}', $this->getHtmlSource());
    }

    public function translateActionDataProvider()
    {
        return array(
            array('test'),
            array(array('test'))
        );
    }
}
