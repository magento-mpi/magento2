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
 * @package    Mage_Oscommerce
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * osCommerce admin controller
 * 
 * @author     Kyaw Soe Lynn<vincent@varien.com>
 */
class Mage_Oscommerce_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initailization of action
     */
    protected function _initAction()
    {
        
        $this->loadLayout();
        $this->_setActiveMenu('adminhtml/system_convert_osc');
        return $this;
    }

    /**
     * Initialization of Osc
     *
     * @param idFieldnName string
     * @return Mage_Adminhtml_System_Convert_OscController
     */
    protected function _initOsc($idFieldName = 'id')
    {
        $id = (int) $this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('oscommerce/oscommerce');
        if ($id) {
            $model->load($id);
        }
        
        Mage::register('current_convert_osc', $model);
        return $this;
    }
    
    /**
     * Index osc action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent(
            $this->getLayout()->createBlock('oscommerce/adminhtml_import')
        );
        $this->renderLayout();
    }

    /**
     * Edit osc action
     */
    public function editAction()
    {
        $this->_initOsc();
        $this->loadLayout();
        
        $model = Mage::registry('current_convert_osc');
        $data = Mage::getSingleton('adminhtml/session')->getSystemConvertOscData(true);

        if (!empty($data)) {
            $model->addData($data);
        }
        
        $this->_initAction();
        $this->_addBreadcrumb
                (Mage::helper('adminhtml')->__('Edit osCommerce Profile'),
                 Mage::helper('adminhtml')->__('Edit osCommerce Profile'));
        /**
         * Append edit tabs to left block
         */
        $this->_addLeft($this->getLayout()->createBlock('oscommerce/adminhtml_import_edit_tabs'));
                         
        $this->_addContent($this->getLayout()->createBlock('oscommerce/adminhtml_import_edit'));
        
        $this->renderLayout();      
    }

    /**
     * Create new osc action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save osc action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $this->_initOsc('import_id');
            $model = Mage::registry('current_convert_osc');

            // Prepare saving data
            if (isset($data)) {
                $model->addData($data);
            }

            if (empty($data['port'])) 
                $data['port'] = Mage_Oscommerce_Model_Oscommerce::DEFAULT_PORT;
            
            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('osCommerce Profile was successfully saved'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setSystemConvertOscData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id'=>$model->getId())));
                return;
            }
        }
        if ($this->getRequest()->getParam('continue')) {
            $this->_redirect('*/*/edit', array('id'=>$model->getId()));
        } else {
            $this->_redirect('*/*');
        }
    }
    
    public function runAction()
    {
        $this->_initOsc();
        $model = Mage::registry('current_convert_osc');
        //$model->importStores(); // done.
        //$model->getResource()->importCustomers($model);
        //$model->getResource()->importCategories($model);
//      echo '<pre>';
        // fixed for multibyte characters
        if ($prefix = $model->getTablePrefix()) {
            $model->getResource()->setTablePrefix($prefix);
        }
        
        Mage::app()->cleanCache();
        
        $statistic = array();
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');
        
        // Setting Locale for stores
        $locales = explode("|",$this->getRequest()->getParam('store_locale'));
        $storeLocales = array();
        if ($locales) foreach($locales as $locale) {
            $localeCode = explode(':', $locale);
            $storeLocales[$localeCode[0]] = $localeCode[1];
        }
        
        $model->getResource()->setStoreLocales($storeLocales);
        // End setting Locale for stores
        
        if ($prefixPath = $this->getRequest()->getParam('images_path')) {
            $model->getResource()->setPrefixPath($prefixPath);
        }
        
        //$isUnderDefaultWebsite = $this->getRequest()->getParam('under_default_website') ? true: false;
        $websiteId = $this->getRequest()->getParam('website_id');
        $websiteCode = $this->getRequest()->getParam('website_code');
        $options = $this->getRequest()->getParam('import');

        if (!$websiteId) {
            $model->getResource()->setWebsiteCode($websiteCode);
            $model->getResource()->createWebsite($model);
        } else {
            $model->getResource()->createWebsite($model, $websiteId);
        }
        $model->getResource()->importStores($model);
        $model->getResource()->importTaxClasses();

        if (isset($options['categories'])) {
            //$statistic = $model->getResource()->getResultStatistic();
            $model->getResource()->importCategories($model);
            $model->getResource()->setIsProductWithCategories(true);
            
        }
        if (isset($options['products'])) {
            $model->getResource()->importProducts($model);
        }        
        if (isset($options['customers'])) {
            $model->getResource()->importCustomers($model);
        } 
        if (isset($options['customers']) && isset($options['orders'])) {
            $model->getResource()->importOrders($model);
        }
        $this->getResponse()->setBody(Zend_Json::encode($model->getResource()->getResultStatistic()));
    }
    
    /**
     * Delete osc action
     */
    public function deleteAction()
    {
        $this->_initOsc();
        $model = Mage::registry('current_convert_osc');
        if ($model->getId()) {
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('osCommerce profile was deleted'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/system_convert_osc');
    }    
    
    /**
     * Ajax checking store
     *
     */
    public function checkStoreAction()
    {
        $this->_initOsc();
        $model = Mage::registry('current_convert_osc');
        if ($model->getId()) {
            try {
                $stores = $model->getResource()->getStores();
    
                $locales = Mage::app()->getLocale()->getOptionLocales();
                $options = '';
    //            $localeCode = array();
                foreach ($locales as $locale) {
                    $options .= "<option value='".$locale['value']."' ".($locale['value']=='en_US'?'selected':'').">{$locale['label']}</option>";
    //                if (!isset($localCode[substr($locale['value'],0,2)]))
    //                $localCode[substr($locale['value'],0,2)] = $locale['value'];
                }
                $html = '';
                if ($stores) {
                    $html .= "<table>\n";
                    foreach ($stores as $store) {
                        $html .= "<tr><td style='width: 100px'>".iconv("ISO-8859-1", "UTF-8", $store['name'])." Store</td><td>";
                        $html .= "<select id='store_locale_{$store['code']}' name='store[{$store['code']}'";
                        $html .= ">{$options}</select>";
                        $html .= "</td></tr>\n";
                    }
                    $html .= "</table>\n";
                }
            } catch (Exception $e) {
                $html = "error";
            }
            $this->getResponse()->setBody($html);
        }
    }
    
    public function checkWebsiteCodeAction()
    {

        $this->_initOsc();
        $model = Mage::registry('current_convert_osc');
        if ($model->getId()) {
            $website = Mage::getModel('core/website');
            $collections = $website->getCollection();
            $result = 'false';
            $websiteCode = $this->getRequest()->getParam('website_code');
            if ($collections) foreach ($collections as $collection) {
                if ($collection->getCode() == $websiteCode) {
                    $result = 'true';
                }
            }
            $this->getResponse()->setBody($result);
        }
    }
}
