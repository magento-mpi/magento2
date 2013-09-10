<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locate all payment methods in the system and verify declaration of their blocks
 */
class Magento_Test_Integrity_Magento_Payment_MethodsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $methodClass
     * @param string $code
     * @dataProvider paymentMethodDataProvider
     * @throws Exception on various assertion failures
     */
    public function testPaymentMethod($code, $methodClass)
    {
        /** @var $blockFactory Magento_Core_Model_BlockFactory */
        $blockFactory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_BlockFactory');
        $storeId = Mage::app()->getStore()->getId();
        /** @var $model Magento_Payment_Model_Method_Abstract */
        if (empty($methodClass)) {
            /**
             * Note that $code is not whatever the payment method getCode() returns
             */
            $this->fail("Model of '{$code}' payment method is not found."); // prevent fatal error
        }
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create($methodClass);
        $this->assertNotEmpty($model->getTitle());
        foreach (array($model->getFormBlockType(), $model->getInfoBlockType()) as $blockClass) {
            $message = "Block class: {$blockClass}";
            /** @var $block Magento_Core_Block_Template */
            $block = $blockFactory->createBlock($blockClass);
            $block->setArea('frontend');
            $this->assertFileExists($block->getTemplateFile(), $message);
            if ($model->canUseInternal()) {
                try {
                    Mage::app()->getStore()->setId(Magento_Core_Model_AppInterface::ADMIN_STORE_ID);
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
        /** @var $helper Magento_Payment_Helper_Data */
        $helper = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Payment_Helper_Data');
        $result = array();
        foreach ($helper->getPaymentMethods() as $code => $method) {
            $result[] = array($code, $method['model']);
        }
        return $result;
    }
}
