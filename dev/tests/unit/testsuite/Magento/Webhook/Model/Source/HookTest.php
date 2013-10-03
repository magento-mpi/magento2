<?php
/**
 * \Magento\Webhook\Model\Source\Hook
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Source;

class HookTest extends \Magento\Webhook\Model\Source\Pkg
{
    public function testGetTopicsForForm()
    {
        $unitUnderTest = new \Magento\Webhook\Model\Source\Hook($this->_mockConfig);
        $elements = $unitUnderTest->getTopicsForForm();
        $this->_assertElements($elements);

        // Verify that we return cached results
        $secondResult = $unitUnderTest->getTopicsForForm();
        $this->assertEquals($elements, $secondResult);
    }
}
