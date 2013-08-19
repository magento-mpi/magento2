<?php
/**
 * Mage_Webhook_Model_Source_Hook
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Source_HookTest extends Mage_Webhook_Model_Source_Pkg
{
    public function testGetTopicsForForm()
    {
        $unitUnderTest = new Mage_Webhook_Model_Source_Hook($this->_mockConfig);
        $elements = $unitUnderTest->getTopicsForForm();
        $this->_assertElements($elements);

        // Verify that we return cached results
        $secondResult = $unitUnderTest->getTopicsForForm();
        $this->assertEquals($elements, $secondResult);
    }
}