<?php

/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Selenium_Uimap_FieldsetTest extends Mage_PHPUnit_TestCase
{
    public function test__construct()
    {
        $fieldsetContainer = array();
        $instance = new Mage_Selenium_Uimap_Fieldset('', $fieldsetContainer);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Fieldset', $instance);
    }
}