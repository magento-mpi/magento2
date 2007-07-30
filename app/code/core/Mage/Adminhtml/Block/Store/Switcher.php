<?php
/**
 * Store switcher block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Store_Switcher extends Mage_Core_Block_Template
{
    protected $_storeIds;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('store/switcher.phtml');
        $this->setUseConfirm(true);
    }
    
    public function getWebsiteCollection()
    {
        return Mage::getModel('core/website')->getResourceCollection()
            ->load();
    }
    
    public function getStores($website)
    {
        $stores = $website->getStoreCollection();
        if (!empty($this->_storeIds)) {
            $stores->addIdFilter($this->_storeIds);
        }
        return $stores->load();
    }
    
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return Mage::getUrl('*/*/*', array('_current'=>true, 'store'=>null));
    }
    
    public function getStoreId()
    {
        return $this->getRequest()->getParam('store');
    }
    
    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }
}
