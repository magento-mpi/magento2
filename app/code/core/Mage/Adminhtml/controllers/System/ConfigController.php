<?php
/**
 * config controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_System_ConfigController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        $this->setFlag('index', 'no-preDispatch', true);
        return parent::_construct();
    }

    public function indexAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->loadLayout('baseframe');

        $this->_setActiveMenu('system/config');

        $this->_addBreadcrumb(__('System'), __('System'), Mage::getUrl('*/system'));

        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/system_config_tabs')->initTabs());

        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_config_edit')->initForm());

        $this->_addJs($this->getLayout()->createBlock('core/template')->setTemplate('system/config/js.phtml'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        try {
            Mage::getResourceModel('adminhtml/config')->saveSectionPost(
                $this->getRequest()->getParam('section'),
                $this->getRequest()->getParam('website'),
                $this->getRequest()->getParam('store'),
                $this->getRequest()->getPost('groups')
            );
            Mage::getSingleton('adminhtml/session')->addSuccess(__('Configuration Successfully Saved'));
            $this->_redirect('*/*/edit', array('_current'=>array('section', 'website', 'store')));
            return;
        } catch( Exception $e ) {
            Mage::getSingleton('adminhtml/session')->addError(nl2br($e->getMessage()));
            $this->_redirect('*/*/edit', array('_current'=>array('section', 'website', 'store')));
        }
    }    

    public function exportTableratesAction()
    {
        $websiteModel = Mage::getModel('core/website')->load($this->getRequest()->getParam('website'));

        if ($this->getRequest()->getParam('conditionName')) {
            $conditionName = $this->getRequest()->getParam('conditionName');
        } else {
            $conditionName = $websiteModel->getConfig('carriers/tablerate/condition_name');
        }

        $tableratesCollection = Mage::getResourceModel('shipping/carrier_tablerate_collection');
        $tableratesCollection->setConditionFilter($conditionName);
        $tableratesCollection->setWebsiteFilter($websiteModel->getId());
        $tableratesCollection->load();

        $csv = '';                                            
        
        $conditionName = Mage::getModel('shipping/carrier_tablerate')->getCode('condition_name_short', $conditionName);
        
        $csvHeader = array('"Country"', '"Region/State"', '"Zip"', '"'.$conditionName.'"', '"Shipping Price"');
        $csv .= implode(',', $csvHeader)."\n";
        
        foreach ($tableratesCollection->getItems() as $item) {
            if ($item->getData('dest_country') == '') {
                $country = '*';
            } else {
                $country = $item->getData('dest_country');
            }
            if ($item->getData('dest_region') == '') {
                $region = '*';
            } else {
                $region = $item->getData('dest_region');
            }
            if ($item->getData('dest_zip') == '') {
                $zip = '*';
            } else {
                $zip = $item->getData('dest_zip');
            }
            $csvData = array('"'.str_replace('"', '""', $country).'"',
                               '"'.str_replace('"', '""', $region).'"',
                               '"'.str_replace('"', '""', $zip).'"',
                               '"'.str_replace('"', '""', $item->getData('condition_value')).'"',
                               '"'.str_replace('"', '""', $item->getData('price')).'"',
                              );
            $csv .= implode(',', $csvData)."\n";
        }
        
        header("Content-disposition: attachment; filename=tablerates.csv");
        echo $csv;
    }
}
