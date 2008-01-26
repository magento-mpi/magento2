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
    Insert into `core_email_template` Set
        `template_code` = 'Product alert for changed price',
        `template_text` = 'Hello {{var customer.name}},<br /><br />Product {{var product.name}} changed price. Please go to <a href = \"{{var product.getProductUrl()}}\">{{var product.name}}</a><br />
            <br /> If you want to unsubscribe from this alert on this product please <a href=\"{{var alert.getUnsubscribeUrl}}\">click here</a>',
        `template_subject` = 'Product {{var product.name}} changed price.',
        `added_at` = now(),
        `modified_at` = now()
    ;
");
$this->run("
    Insert into `core_email_template` Set
        `template_code` = 'Product alert for back in stock',
        `template_text` = 'Hello {{var customer.name}},<br /><br />Product {{var product.name}} back in stock. Please go to <a href = \"{{var product.getProductUrl()}}\">{{var product.name}}</a><br />
            <br /> If you want to unsubscribe from this alert on this product please <a href=\"{{var alert.getUnsubscribeUrl}}\">click here</a>.',
        `template_subject`='Product {{var product.name}} back in stock.',
        `added_at` = now(),
        `modified_at` = now()
    ;
");
