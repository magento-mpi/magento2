<?php
/**
 * Templates collection
 * 
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Core_Model_Mysql4_Email_Template_Collection extends Varien_Data_Collection_Db
{
    /**
     * Template table name
     *
     * @var string
     */
    protected $_templateTable;
    
    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('core_read'));
        $this->_templateTable = Mage::getSingleton('core/resource')->getTableName('core/email_template');
        $this->_sqlSelect->from($this->_templateTable, array('template_id','template_code',
                                                             'template_type',
                                                             'template_subject','template_sender_name',
                                                             'template_sender_email',
                                                             'added_at',
                                                             'modified_at'));
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('core/email_template'));
    }
                
    public function toOptionArray()
    {
    	return $this->_toOptionArray('template_id', 'template_code');
    }
    
}