<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Resource_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for Saas_PrintedTemplate_Model_Resource_Template::_prepareDataForSave
     */
    public function testSave()
    {
        $model = Mage::getModel('Saas_PrintedTemplate_Model_Template');
        $data = array(
            'name' => 'Test invoice',
            'entity_type' => 'invoice'
        );

        $date = date('Y-m-d');
        $model->setData($data)->save();
        $this->assertNotNull($model->getTemplateId());
        $this->assertEquals($date, date('Y-m-d', strtotime($model->getCreatedAt())));
        $this->assertEquals($date, date('Y-m-d', strtotime($model->getUpdatedAt())));

        $model->setHeader('bla-bla-bla')->setUpdatedAt('0000-00-00 00:00:00')->save();
        $this->assertNotEquals('0000-00-00 00:00:00', $model->getUpdatedAt());

        Mage::getObjectManager()->get('Mage_Core_Model_Config_Scope')
            ->setCurrentScope(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        $model->delete();
    }
}
