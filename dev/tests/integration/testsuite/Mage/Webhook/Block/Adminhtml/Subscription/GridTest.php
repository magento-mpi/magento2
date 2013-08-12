<?php
/**
 * Mage_Webhook_Block_AdminHtml_Subscription_Grid
 *
 * @magentoAppArea adminhtml
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Subscription_GridTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareColumns()
    {
        $layout = Mage::getObjectManager()->create('Magento_Core_Model_Layout');
        /** @var Mage_Webhook_Block_Adminhtml_Subscription_Grid $block */
        $block = $layout->addBlock('Mage_Webhook_Block_Adminhtml_Subscription_Grid');
        $block->toHtml();

        $columns = array(
            'id' => array(
                'header'    => 'ID',
                'index'     => 'subscription_id',
            ),
            'name' => array(
                'header'    => 'Name',
                'index'     => 'name',
            ),
            'version' => array(
                'header'    => 'Version',
                'index'     => 'version',
            ),
            'endpoint_url' => array(
                'header'    => 'Endpoint URL',
                'index'     => 'endpoint_url',
            ),
            'status' => array(
                'header'    => 'Status',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => array(
                    Mage_Webhook_Model_Subscription::STATUS_ACTIVE => 'Active',
                    Mage_Webhook_Model_Subscription::STATUS_REVOKED => 'Revoked',
                    Mage_Webhook_Model_Subscription::STATUS_INACTIVE => 'Inactive',
                )
            ),
            'action' => array(
                'header'    =>  'Action',
                'renderer'  =>  'Mage_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_Action'
            )
        );


        foreach ($block->getColumns() as $column) {
            /** @var Magento_Backend_Block_Widget_Grid_Column $column */
            $columnId = $column->getData('id');
            $this->assertTrue(isset($columns[$columnId]));
            $this->assertEquals($columns[$columnId]['header'], $column->getData('header'));
            $this->assertEquals($columns[$columnId]['index'], $column->getData('index'));

            if (isset($columns[$columnId]['type'])) {
                $this->assertEquals($columns[$columnId]['type'], $column->getData('type'));
            }

            if (isset($columns[$columnId]['renderer'])) {
                $this->assertEquals($columns[$columnId]['renderer'], $column->getData('renderer'));
            }

            if (isset($columns[$columnId]['options'])) {
                $this->assertEquals($columns[$columnId]['options'], $column->getData('options'));
            }
        }
    }
}