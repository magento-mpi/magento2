<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Layout_UpdateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_DesignEditor_Model_Layout_Update::__beforeSave
     * @magentoDbIsolation enabled
     */
    public function testBeforeSave()
    {
        /** @var $model Mage_DesignEditor_Model_Layout_Update */
        $model = Mage::getObjectManager()->get('Mage_DesignEditor_Model_Layout_Update');
        $model->setData(array(
            'handle' => 'layout_update_test'
        ));
        $model->save();
        $this->assertTrue($model->getIsVde());
        $model->delete();
    }
}
