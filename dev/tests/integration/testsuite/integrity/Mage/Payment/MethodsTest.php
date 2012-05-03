<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locate all payment methods in the system and verify declaration of their blocks
 *
 * @group integrity
 */
class Integrity_Mage_Payment_MethodsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $methodClass
     * @dataProvider paymentMethodDataProvider
     */
    public function testFormInfoTemplates($methodClass)
    {
        $storeId = Mage::app()->getStore()->getId();
        /** @var $model Mage_Payment_Model_Method_Abstract */
        $model = new $methodClass;
        foreach (array($model->getFormBlockType(), $model->getInfoBlockType()) as $blockClass) {
            $message = "Block class: {$blockClass}";
            $block = new $blockClass;
            $block->setArea('frontend');
            $this->assertFileExists($block->getTemplateFile(), $message);
            if ($model->canUseInternal()) {
                try {
                    Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
                    $block->setArea('adminhtml');
                    $this->assertFileExists($block->getTemplateFile(), $message);
                    Mage::app()->getStore()->setId($storeId);
                } catch (Exception $e) {
                    Mage::app()->getStore()->setId($storeId);
                    throw $e;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function paymentMethodDataProvider()
    {
        $helper = new Mage_Payment_Helper_Data;
        $result = array();
        foreach ($helper->getPaymentMethods() as $method) {
            $result[] = array($method['model']);
        }
        return $result;
    }
}
