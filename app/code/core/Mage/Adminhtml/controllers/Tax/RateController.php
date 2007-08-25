<?php
/**
 * Adminhtml tax rate controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Tax_RateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Tax Rates'), __('Tax Rates'))
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_rate_toolbar_add', 'tax_rate_toolbar')
                    ->assign('createUrl', Mage::getUrl('*/tax_rate/add'))
                    ->assign('header', __('Tax Rates'))
            )
            ->_addContent($this->getLayout()->createBlock('adminhtml/tax_rate_grid', 'tax_rate_grid'))
            ->renderLayout();
    }

    public function addAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Tax Rates'), __('Tax Rates'), Mage::getUrl('*/tax_rate'))
            ->_addBreadcrumb(__('New Tax Rate'), __('New Tax Rate'))
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_rate_toolbar_save')
                ->assign('header', __('Add New Tax Rate'))
                ->assign('form', $this->getLayout()->createBlock('adminhtml/tax_rate_form_add'))
            )
            ->renderLayout();
    }

    public function saveAction()
    {
        if( $postData = $this->getRequest()->getPost() ) {
            try {
                $rateModel = Mage::getSingleton('tax/rate');
                $rateModel->setData($postData);
                $rateModel->save();
                $this->getResponse()->setRedirect(Mage::getUrl("*/*/"));
                Mage::getSingleton('adminhtml/session')->addSuccess('Tax rate successfully saved.');
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            } catch (Exception $e) {
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                }
                #Mage::getSingleton('adminhtml/session')->addError('Error while saving this rate. Please try again later.');
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }

    public function editAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Tax Rates'), __('Tax Rates'), Mage::getUrl('*/tax_rate'))
            ->_addBreadcrumb(__('Edit Tax Rate'), __('Edit Tax Rate'))
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_rate_toolbar_save')
                ->assign('header', __('Edit Tax Rate'))
                ->assign('form', $this->getLayout()->createBlock('adminhtml/tax_rate_form_add'))
            )
            ->renderLayout();
    }

    public function deleteAction()
    {
        if( $rateId = $this->getRequest()->getParam('rate') ) {
            try {
                $rateModel = Mage::getSingleton('tax/rate');
                $rateModel->setTaxRateId($rateId);
                $rateModel->delete();
                $this->getResponse()->setRedirect(Mage::getUrl("*/*/"));
                Mage::getSingleton('adminhtml/session')->addSuccess('Tax rate successfully deleted.');
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            } catch (Exception $e) {
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                }
                Mage::getSingleton('adminhtml/session')->addError('Error while deleting this rate. Please try again later.');
            }
        }
    }

    /**
     * Export rates grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'rates.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/tax_rate_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Export rates grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'rates.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/tax_rate_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('sales/tax_rates')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Tax'), __('Tax'));
        return $this;
    }


    protected function _sendUploadResponse($fileName, $content)
    {
        header('HTTP/1.1 200 OK');
        header('Content-Disposition: attachment; filename='.$fileName);
        header('Last-Modified: '.date('r'));
        header("Accept-Ranges: bytes");
        header("Content-Length: ".sizeof($content));
        header("Content-type: application/octet-stream");
        echo $content;
    }

    public function importExportAction()
    {
    	$this->loadLayout('baseframe')
    		->_setActiveMenu('sales/tax_importExport')
    		->_addContent($this->getLayout()->createBlock('adminhtml/tax_rate_importExport'))
    		->renderLayout();
    }

    public function importPostAction()
    {
    	$result = false;
    	if ($this->getRequest()->isPost()
    		&& !empty($_FILES['import_rates_file']['tmp_name'])) {
    		try {
    			$this->_importRates();
				Mage::getSingleton('adminhtml/session')->addSuccess('Tax rates file has been imported successfully');
    		} catch (Exception $e) {
    			Mage::getSingleton('adminhtml/session')->addError('Error during import: '.$e);
    		}
    	} else {

    		Mage::getSingleton('adminhtml/session')->addError('Invalid file upload attempt');

    	}
    	$this->_redirect('*/*/importExport');
    }

    protected function _importRates()
    {
    	$filename = $_FILES['import_rates_file']['tmp_name'];
    	$rows = array();

		$rates = $this->_importFileToArray($filename);

    	$rateModel = Mage::getModel('tax/rate');
    	$rateDataModel = Mage::getModel('tax/rate_data');

    	$rateModel->deleteAllRates();

    	foreach ($rates as $rate) {
    		$rateModel->setData($rate)->save();
    	}

    	return true;
    }

    protected function _importFileToArray($filename)
    {
    	$rateTypes = array();
    	$typeCollection = Mage::getResourceModel('tax/rate_type_collection')->load();
    	foreach ($typeCollection as $type) {
    		$rateTypes[$type->getTypeName()] = $type->getTypeId();
    	}

    	$regions = array();
    	$regionCollection = Mage::getResourceModel('directory/region_collection')
    		->addCountryFilter(223)->load();
    	foreach ($regionCollection as $region) {
    		$regions[$region->getCode()] = $region->getRegionId();
    	}

    	$fp = fopen($filename, 'r');
    	$cols = array();
    	$rates = array();
    	while ($row = fgetcsv($fp, 300, ',', '"')) {
    		if (empty($cols)) {
    			$regionName = __('State/Province');
    			$postcodeName = __('Zip/Postal Code');
    			foreach ($row as $k=>$v) {
    				if ($v==$regionName) {
    					$cols[$k] = 'region_name';
    				} elseif ($v==$postcodeName) {
    					$cols[$k] = 'postcode';
	    				} elseif (!empty($rateTypes[$v])) {
	    					$cols[$k] = $rateTypes[$v];
	    				}
    			}
    			continue;
    		}
			$rate = array('tax_region_id'=>null, 'tax_postcode'=>null);
    		foreach ($row as $k=>$v) {
    			switch ($cols[$k]) {
    				case 'region_name':
    					$rate['tax_region_id'] = $regions[$v];
    					break;

    				case 'postcode':
						$rate['tax_postcode'] = $v;
    					break;

    				default:
    					$rate['rate_data'][$cols[$k]] = $v;
    			}
    		}
    		$rates[] = $rate;
    	}
    	fclose($fp);
    	@unlink($filename);

    	return $rates;
    }

    public function exportPostAction()
    {
    	$rateTypes = array();
    	$typeCollection = Mage::getResourceModel('tax/rate_type_collection')->load();
    	foreach ($typeCollection as $type) {
    		$rateTypes[$type->getTypeId()] = $type->getTypeName();
    	}

    	$rateCollection = Mage::getResourceModel('tax/rate_collection')->addAttributes()->load();
    	$content = '';
    	foreach ($rateCollection as $rate) {
    		if (empty($content)) {
    			$content .= '"'.__('State/Province').'","'.__('Zip/Postal Code').'"';
    			$template = '"{{region_name}}","{{tax_postcode}}"';
    			foreach ($rate->getData() as $k=>$v) {
    				if (!preg_match('#^rate_value_([0-9]+)$#', $k, $m)) {
    					continue;
    				}
    				$content.= ',"'.$rateTypes[$m[1]].'"';
    				$template.= ',"{{'.$k.'}}"';
    			}
    			$content.= "\r\n";
    		}
    		$content.= $rate->toString($template)."\r\n";
    	}

    	$fileName = 'tax_rates.csv';

        header('HTTP/1.1 200 OK');
        header('Content-Disposition: attachment; filename='.$fileName);
        header('Last-Modified: '.date('r'));
        header("Accept-Ranges: bytes");
        header("Content-Length: ".sizeof($content));
        header("Content-type: application/octet-stream");
        echo $content;
        exit;
    }

    protected function _isAllowed()
    {

    	switch ($this->getRequest()->getActionName()) {
            case 'importExport':
                return Mage::getSingleton('admin/session')->isAllowed('sales/tax/import_export');
                break;
            case 'index':
                return Mage::getSingleton('admin/session')->isAllowed('sales/tax/rates');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('sales/tax/rates');
                break;
        }
    }
}