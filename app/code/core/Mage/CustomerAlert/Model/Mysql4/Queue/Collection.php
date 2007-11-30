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
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter queue collection.
 *
 * @category   Mage
 * @package    Mage_CustomerAlert
 * @author     Vasily Selivanov <vasily@varien.com>
 */

class Mage_CustomerAlert_Model_Mysql4_Queue_Collection extends Mage_Newsletter_Model_Mysql4_Queue_Collection
{
     /**
     * Initializes collection
     */
    protected function _construct()
    {
        $this->_init('customeralert/queue');
    }
    
    public function addTemplateInfo() {
        $this->getSelect()->joinLeft(array('template'=>$this->getTable('core/email_template')),
            'template.template_id=main_table.template_id',
            array('template_subject','template_sender_name','template_sender_email'));
        $this->_joinedTables['template'] = true;
        return $this;
    }
    
}
