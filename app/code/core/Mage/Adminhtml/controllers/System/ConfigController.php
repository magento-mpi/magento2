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

        $this->_addBreadcrumb(__('System'), __('System Title'), Mage::getUrl('adminhtml/system'));

        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/system_config_tabs')->initTabs());

        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_config_edit')->initForm());

        $this->renderLayout();
    }

    public function saveAction()
    {
        Mage::getResourceModel('adminhtml/config')->saveSectionPost(
            $this->getRequest()->getParam('section'),
            $this->getRequest()->getParam('website'),
            $this->getRequest()->getParam('store'),
            $this->getRequest()->getPost('groups')
        );
        $this->_redirect('*/*/edit', array('_current'=>array('section', 'website', 'store')));
    }    

    public function exportTableratesAction()
    {
        $tableratesCollection = Mage::getResourceModel('shipping/carrier_tablerate_collection');
        $tableratesCollection->setConditionFilter(Mage::getStoreConfig('carriers/tablerate/condition_name'));
        $tableratesCollection->setCountryFilter(223); // TOFIX, FIXME
        $tableratesCollection->setWebsiteFilter(Mage::getModel('core/website')->load($this->getRequest()->getParam('website'))->getId());
        $tableratesCollection->load();

        $csv = '';                                            

        $dataHeader = array();
        $data = array();
        foreach ($tableratesCollection->getItems() as $item) {
            $dataHeader[] = $item->getData('condition_value');
            $data[$item->getData('dest_region_id')][$item->getData('dest_zip')][$item->getData('condition_value')] = array('price'=>$item->getData('price'), 'cost'=>$item->getData('cost'));
        }
        $dataHeader = array_unique($dataHeader);
        sort($dataHeader);
        ksort($data);
        foreach ($data as $k=>$v) {
            ksort($data[$k]);
        }
        
        $conditionName = Mage::getModel('shipping/carrier_tablerate')->getCode('condition_name_short', Mage::getStoreConfig('carriers/tablerate/condition_name'));
        
        $csvHeader = array('"Region"','"ZIP \\ '.$conditionName.'"');
        foreach ($dataHeader as $conditionValue) {
            $csvHeader[] = '"'.str_replace('"', '""', $conditionValue).'"';
        }
        $csv .= implode(',', $csvHeader)."\n";
        
        foreach ($data as $region=>$v) {
            foreach ($data[$region] as $zip=>$v2) {
                $csvData = array();
                $csvData[] = '"'.str_replace('"', '""', $region).'"';
                $csvData[] = '"'.str_replace('"', '""', $zip).'"';
                foreach ($dataHeader as $conditionValue) {
                    if (isset($data[$region][$zip][$conditionValue])) {
                        $csvData[] = '"'.str_replace('"', '""', $data[$region][$zip][$conditionValue]['price']).'"';
                    } else {
                        $csvData[] = '"-1"';
                    }
                }
                $csv .= implode(',', $csvData)."\n";
            }
        }
        
        header("Content-disposition: attachment; filename=tablerates.csv");
        echo $csv;
    }    
}
