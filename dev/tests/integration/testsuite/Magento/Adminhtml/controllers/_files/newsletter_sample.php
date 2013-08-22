<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var Magento_Newsletter_Model_Template $template */
$template = Mage::getModel('Magento_Newsletter_Model_Template');

$templateData = array(
    'template_code'=>'some_unique_code',
    'template_type'=>Magento_Newsletter_Model_Template::TYPE_TEXT,
    'subject'=>'test data2__22',
    'template_sender_email'=>'sender@email.com',
    'template_sender_name'=>'Test Sender Name 222',
    'text'=>'Template Content 222',
);
$template->setData($templateData);
$template->save();
