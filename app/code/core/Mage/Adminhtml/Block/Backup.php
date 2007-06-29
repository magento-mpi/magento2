<?
/**
 * Adminhtml backup page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Backup extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/backup/list.phtml');
    }
    
    public function _beforeToHtml()
    {
        $this->assign('grid', $this->getLayout()->createBlock('adminhtml/backup_grid', 'backup.grid')->toHtml());
        return $this;
    }
   
}
