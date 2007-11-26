<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$this->run("
    Update `core_config_field` Set path = 'sendfriend/email/template' Where path = 'sendfriend/emTemplates/template';
    Update `core_config_field` Set path = 'sendfriend/email' Where path = 'sendfriend/emTemplates';
    delete from `core_email_template` where template_code = 'Send product to a friend';
");
$con = $this->getConnection();
$data = array(
    'template_id'           => null,
    'template_code'         => 'Send product to a friend',
    'template_text'         => 'Welcome, {{var name}}<br /><br />Please look at <a href=\"{{var product.productUrl}}\">{{var product.name}}</a><br /><br />Here is message: <br />{{var message}}<br /><br />', 
    'template_type'         => 2,
    'template_subject'      => 'Welcome, {{var name}}',
    'template_sender_name'  => null,
    'template_sender_email' => null,
    'added_at'              => '2007-11-12 13:19:12', 
    'modified_at'           => '2007-11-26 12:29:31'  
);
$con->insert('core_email_template',$data);
$last_insert_id = $con->lastInsertId();
$this->setConfigData('sendfriend/email/template',$last_insert_id);


