<?php
/**
 * Mage_Webhook_Model_Source_Format
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Source_FormatTest extends Mage_Webhook_Model_Source_Pkg
{
    public function testGetFormatsForForm()
    {
        $unitUnderTest = new Mage_Webhook_Model_Source_Format(array('type' => 'blah'));
        $elements = $unitUnderTest->getFormatsForForm();
        $this->_assertElements($elements);

        // Verify that we return cached results
        $secondResult = $unitUnderTest->getFormatsForForm();
        $this->assertEquals($elements, $secondResult);
    }
}