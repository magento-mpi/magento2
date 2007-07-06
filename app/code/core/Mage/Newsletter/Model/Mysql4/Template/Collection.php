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
class Mage_Newsletter_Model_Mysql4_Template_Collection extends Varien_Data_Collection_Db
{
    /**
     * Template table name
     *
     * @var string
     */
    protected $_templateTable;
    
    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('newsletter_read'));
        $this->_templateTable = Mage::getSingleton('core/resource')->getTableName('newsletter/template');
        $this->_sqlSelect->from($this->_templateTable, array('template_id','template_code',
                                                             'template_type',
                                                             'template_subject','template_sender_name',
                                                             'template_sender_email'));
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('newsletter/template'));
    }
    
    /**
     * Load only actual template
     *
     * @return  Mage_Newsletter_Model_Mysql4_Template_Collection
     */
    public function useOnlyActual()
    {
        $this->_sqlSelect->where('template_actual=?', 1);
        
        return $this;
    }
}