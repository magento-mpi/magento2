<?php
/**
 * Magento_Webhook_Model_Source_Format
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Source_FormatTest extends Magento_Webhook_Model_Source_Pkg
{
    public function testGetFormatsForForm()
    {
        $unitUnderTest = new Magento_Webhook_Model_Source_Format(array('type' => 'blah'));
        $elements = $unitUnderTest->getFormatsForForm();
        $this->_assertElements($elements);

        // Verify that we return cached results
        $secondResult = $unitUnderTest->getFormatsForForm();
        $this->assertEquals($elements, $secondResult);
    }
}
