<?php
/**
 * admin config left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Left extends Mage_Adminhtml_Block_Widget
{
    protected $_websiteCode;
    protected $_storeCode;
    protected $_sectionCode;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/widget/tabs.phtml');
        
        $this->_websiteCode = $this->getRequest()->getParam('website');
        $this->_storeCode   = $this->getRequest()->getParam('store');
        $this->_sectionCode = $this->getRequest()->getParam('section');

    }
    
    public function getLinks()
    {
        $links = array(
            new Varien_Object(array(
                'label' => __('global config'),
                'title' => __('global config title'),
                'url'   => Mage::getUrl('adminhtml/system_config', array('section'=>$this->_sectionCode)),
                'class' => is_null($this->_websiteCode) && is_null($this->_storeCode) ? 'active' : ''
            ))
        );
        
        $websites = Mage::getConfig()->getNode('global/websites')->asArray();
        foreach ($websites as $code => $info) {
            $links[] = new Varien_Object(array(
                'label' => __($code),
                'url'   => Mage::getUrl('adminhtml/system_config/edit', array('website'=>$code, 'section'=>$this->_sectionCode)),
                'class' => ($this->_websiteCode == $code && is_null($this->_storeCode)) ? 'active' : ''
            ));
            
            $website = Mage::getModel('core/website')
                ->setCode($code);
            $storeCodes = $website->getStoreCodes();
            foreach ($storeCodes as $storeCode) {
                $links[] = new Varien_Object(array(
                    'label' => __($storeCode),
                    'url'   => Mage::getUrl('adminhtml/system_config/edit', array('website'=>$code, 'store'=>$storeCode, 'section'=>$this->_sectionCode)),
                    'class' => ($this->_storeCode == $storeCode) ? 'subitem active' : 'subitem'
                ));
            }
        }

        
        return $links;
    }

    public function bindBreadcrumbs($breadcrumbs)
    {
        if ($this->_websiteCode) {
            $this->_addBreadcrumb(__('config'), __('config title'), Mage::getUrl('adminhtml/system_config'));
            if ($this->_storeCode) {
                $this->_addBreadcrumb(__($this->_websiteCode), '', Mage::getUrl('adminhtml/system_config',array('website'=>$this->_websiteCode)));
                $this->_addBreadcrumb(($this->_storeCode == 1) ? __('new store') :__($this->_storeCode), '');
            }
            else {
                $this->_addBreadcrumb(($this->_websiteCode == 1) ? __('new website') :__($this->_websiteCode), '');
            }
        }
        else {
            $this->_addBreadcrumb(__('config'), __('config title'));
            $this->_addBreadcrumb(__('global'), __('global title'));
        }
        return $this;
    }
}
