<?
/**
 * Adminhtml system templates page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_System_Email_Template extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/email/template/list.phtml');
    }

    protected function _initChildren()
    {
    	$this->setChild('grid', $this->getLayout()->createBlock('adminhtml/system_email_template_grid', 'email.template.grid'));
        return $this;
    }
    
    public function getCreateUrl()
    {
    	return $this->getUrl('*/*/new');
    }
    
    public function getHeaderText()
    {
    	return __('Transactional Emails');
    }
}
