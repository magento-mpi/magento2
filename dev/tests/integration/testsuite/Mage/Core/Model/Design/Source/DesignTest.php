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

    protected function _getLabelCollection()
    {
        return array(
            array(
                'value' => '1',
                'label' => 'Magento Fluid Design  (incompatible version)'
            ),
            array(
                'value' => '2',
                'label' => 'Magento Demo'
            ),
            array(
                'value' => '3',
                'label' => 'Magento Modern'
            ),
            array(
                'value' => '4',
                'label' => 'Magento Iphone'
            ),
            array(
                'value' => '5',
                'label' => 'Magento Blank'
            ),
            array(
                'value' => '6',
                'label' => 'Magento Demo Blue'
            ),
            array(
                'value' => '7',
                'label' => 'Magento Fixed Design'
            ),
            array(
                'value' => '8',
                'label' => 'Magento Iphone (HTML5)'
            ),
        );
    }
}
