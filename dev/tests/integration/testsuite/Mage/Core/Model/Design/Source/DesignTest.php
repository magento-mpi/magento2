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

class Mage_Core_Model_Design_Source_DesignTest extends PHPUnit_Framework_TestCase
{

    public function testGetAllOptions()
    {
        /** Skipped MAGETWO-3556: Ability for System to Operate w/o Design Theme */
        $this->markTestIncomplete('Skipped MAGETWO-3556: Ability for System to Operate w/o Design Theme');

        /** @var $model Mage_Core_Model_Design_Source_Design */
        $model = Mage::getModel('Mage_Core_Model_Design_Source_Design');
        $labelCollection = $this->_getLabelCollection();

        $this->assertEquals($labelCollection, $model->getAllOptions(false));

        array_unshift($labelCollection, array(
             'value' => '',
             'label' => '-- Please Select --'
        ));
        $this->assertEquals($labelCollection, $model->getAllOptions());
    }

    /**
     * Return sorted by title themes
     *
     * @return array
     */
    protected function _getLabelCollection()
    {
        return array(
            array(
                'value' => '%d',
                'label' => 'Magento Blank'
            ),
            array(
                'value' => '%d',
                'label' => 'Magento Demo'
            ),
            array(
                'value' => '%d',
                'label' => 'Magento Demo Blue'
            ),
            array(
                'value' => '%d',
                'label' => 'Magento Fixed Design'
            ),
            array(
                'value' => '%d',
                'label' => 'Magento Fluid Design  (incompatible version)'
            ),
            array(
                'value' => '%d',
                'label' => 'Magento Iphone'
            ),
            array(
                'value' => '%d',
                'label' => 'Magento Iphone (HTML5)'
            ),
            array(
                'value' => '%d',
                'label' => 'Magento Modern'
            )
        );
    }
}
