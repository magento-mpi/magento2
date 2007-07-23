<?
/**
 * Adminhtml newsletter templates page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Template extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('newsletter/template/list.phtml');
    }

       
    protected function _initChildren()
    {    	
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/newsletter_template_grid', 'newsletter.template.grid'));
        return $this;
    }
	
    public function getCreateUrl()
    {
    	return $this->getUrl('*/*/new');
    }
    
    public function getHeaderText()
    {
    	return __('Newsletter Templates');
    }
}
