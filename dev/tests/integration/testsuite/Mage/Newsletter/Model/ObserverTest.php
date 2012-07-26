<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Newsletter
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Newsletter_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    public function testEmailTemplateFilterBefore()
    {
        $model = new Mage_Newsletter_Model_Template;
        $id = uniqid();
        $subscriber = new Mage_Newsletter_Model_Subscriber(array('store_id' => $id));
        $model->setTemplateText('{{var subscriber.getStoreId()}}');
        $this->assertEquals($id, $model->getProcessedTemplate(array('subscriber' => $subscriber)));
    }
}
