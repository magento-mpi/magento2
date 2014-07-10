<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Controller\Adminhtml;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Controller\RegistryConstants;

/**
 * Adminhtml tax rate controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rate extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Tax\Service\V1\TaxRateServiceInterface
     */
    protected $_taxRateService;

    /**
     * @var \Magento\Tax\Service\V1\Data\TaxRateBuilder
     */
    protected $_taxRateBuilder;

    /**
     * @var \Magento\Tax\Service\V1\Data\ZipRangeBuilder
     */
    protected $_zipRangeBuilder;

    /**
     * @var \Magento\Tax\Service\V1\Data\TaxRateTitleBuilder
     */
    protected $_taxRateTitleBuilder;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Tax\Service\V1\TaxRateServiceInterface $taxRateService
     * @param \Magento\Tax\Service\V1\Data\TaxRateBuilder $taxRateBuilder
     * @param \Magento\Tax\Service\V1\Data\ZipRangeBuilder $zipRangeBuilder
     * @param \Magento\Tax\Service\V1\Data\TaxRateTitleBuilder $taxRateTitleBuilder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Tax\Service\V1\TaxRateServiceInterface $taxRateService,
        \Magento\Tax\Service\V1\Data\TaxRateBuilder $taxRateBuilder,
        \Magento\Tax\Service\V1\Data\ZipRangeBuilder $zipRangeBuilder,
        \Magento\Tax\Service\V1\Data\TaxRateTitleBuilder $taxRateTitleBuilder
    ) {
        $this->_fileFactory = $fileFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_taxRateService = $taxRateService;
        $this->_taxRateBuilder = $taxRateBuilder;
        $this->_zipRangeBuilder = $zipRangeBuilder;
        $this->_taxRateTitleBuilder = $taxRateTitleBuilder;
        parent::__construct($context);
    }

    /**
     * Show Main Grid
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Tax Zones and Rates'));

        $this->_initAction()->_addBreadcrumb(__('Manage Tax Rates'), __('Manage Tax Rates'));
        $this->_view->renderLayout();
    }

    /**
     * Show Add Form
     *
     * @return void
     */
    public function addAction()
    {
        $this->_title->add(__('Tax Zones and Rates'));

        $this->_title->add(__('New Tax Rate'));

        $this->_coreRegistry->register(
            RegistryConstants::CURRENT_TAX_RATE_FORM_DATA,
            $this->_objectManager->get('Magento\Backend\Model\Session'
        )->getFormData(true));

        $this->_initAction()->_addBreadcrumb(
            __('Manage Tax Rates'),
            __('Manage Tax Rates'),
            $this->getUrl('tax/rate')
        )->_addBreadcrumb(
            __('New Tax Rate'),
            __('New Tax Rate')
        )->_addContent(
            $this->_view->getLayout()->createBlock(
                'Magento\Tax\Block\Adminhtml\Rate\Toolbar\Save'
            )->assign(
                'header',
                __('Add New Tax Rate')
            )->assign(
                'form',
                $this->_view->getLayout()->createBlock('Magento\Tax\Block\Adminhtml\Rate\Form', 'tax_rate_form')
            )
        );
        $this->_view->renderLayout();
    }

    /**
     * Save Rate and Data
     *
     * @return bool
     */
    public function saveAction()
    {
        $ratePost = $this->getRequest()->getPost();
        if ($ratePost) {
            $rateId = $this->getRequest()->getParam('tax_calculation_rate_id');
            if ($rateId) {
                try {
                    $this->_taxRateService->getTaxRate($rateId);
                } catch (NoSuchEntityException $e) {
                    unset($ratePost['tax_calculation_rate_id']);
                }
            }

            try {
                $taxData = $this->populateTaxRateData($ratePost);
                if (isset($ratePost['tax_calculation_rate_id'])) {
                    $this->_taxRateService->updateTaxRate($taxData);
                } else {
                    $this->_taxRateService->createTaxRate($taxData);
                }

                $this->messageManager->addSuccess(__('The tax rate has been saved.'));
                $this->getResponse()->setRedirect($this->getUrl("*/*/"));
                return true;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($ratePost);
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
            return;
        }
        $this->getResponse()->setRedirect($this->getUrl('tax/rate'));
    }

    /**
     * Save Tax Rate via AJAX
     *
     * @return void
     */
    public function ajaxSaveAction()
    {
        $responseContent = '';
        try {
            $rateData = $this->_processRateData($this->getRequest()->getPost());
            $taxRate = $this->populateTaxRateData($rateData);
            $taxRateId = $taxRate->getId();
            if ($taxRateId) {
                $this->_taxRateService->updateTaxRate($taxRate);
            } else {
                $taxRate = $this->_taxRateService->createTaxRate($taxRate);
                $taxRateId = $taxRate->getId();
            }
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array(
                    'success' => true,
                    'error_message' => '',
                    'tax_calculation_rate_id' => $taxRate->getId(),
                    'code' => $taxRate->getCode()
                )
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array(
                    'success' => false,
                    'error_message' => $e->getMessage(),
                    'tax_calculation_rate_id' => '',
                    'code' => ''
                )
            );
        } catch (\Exception $e) {
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array(
                    'success' => false,
                    'error_message' => __('Something went wrong saving this rate.'),
                    'tax_calculation_rate_id' => '',
                    'code' => ''
                )
            );
        }
        $this->getResponse()->representJson($responseContent);
    }

    /**
     * Validate/Filter Rate Data
     *
     * @param array $rateData
     * @return array
     */
    protected function _processRateData($rateData)
    {
        $result = array();
        foreach ($rateData as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->_processRateData($value);
            } else {
                $result[$key] = trim(strip_tags($value));
            }
        }
        return $result;
    }

    /**
     * Show Edit Form
     *
     * @return void
     */
    public function editAction()
    {
        $this->_title->add(__('Tax Zones and Rates'));

        $rateId = (int)$this->getRequest()->getParam('rate');
        $this->_coreRegistry->register(RegistryConstants::CURRENT_TAX_RATE_ID, $rateId);
        try {
            $taxRateDataObject = $this->_taxRateService->getTaxRate($rateId);
        } catch (NoSuchEntityException $e) {
            $this->getResponse()->setRedirect($this->getUrl("*/*/"));
            return;
        }

        $this->_title->add(sprintf("%s", $taxRateDataObject->getCode()));

        $this->_initAction()->_addBreadcrumb(
            __('Manage Tax Rates'),
            __('Manage Tax Rates'),
            $this->getUrl('tax/rate')
        )->_addBreadcrumb(
            __('Edit Tax Rate'),
            __('Edit Tax Rate')
        )->_addContent(
            $this->_view->getLayout()->createBlock(
                'Magento\Tax\Block\Adminhtml\Rate\Toolbar\Save'
            )->assign(
                'header',
                __('Edit Tax Rate')
            )->assign(
                'form',
                $this->_view->getLayout()->createBlock(
                    'Magento\Tax\Block\Adminhtml\Rate\Form',
                    'tax_rate_form'
                )->setShowLegend(
                    true
                )
            )
        );
        $this->_view->renderLayout();
    }

    /**
     * Delete Rate and Data
     *
     * @return bool
     */
    public function deleteAction()
    {
        if ($rateId = $this->getRequest()->getParam('rate')) {
            try {
                $this->_taxRateService->deleteTaxRate($rateId);

                $this->messageManager->addSuccess(__('The tax rate has been deleted.'));
                $this->getResponse()->setRedirect($this->getUrl("*/*/"));
                return true;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addError(
                    __('Something went wrong deleting this rate because of an incorrect rate ID.')
                );
                $this->getResponse()->setRedirect($this->getUrl('tax/*/'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong deleting this rate.'));
            }

            if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                $this->getResponse()->setRedirect($referer);
            } else {
                $this->getResponse()->setRedirect($this->getUrl("*/*/"));
            }
        }
    }

    /**
     * Delete Tax Rate via AJAX
     *
     * @return void
     */
    public function ajaxDeleteAction()
    {
        $rateId = (int)$this->getRequest()->getParam('tax_calculation_rate_id');
        try {
            $this->_taxRateService->deleteTaxRate($rateId);
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array('success' => true, 'error_message' => '')
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array('success' => false, 'error_message' => $e->getMessage())
            );
        } catch (\Exception $e) {
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array('success' => false, 'error_message' => __('An error occurred while deleting this tax rate.'))
            );
        }
        $this->getResponse()->representJson($responseContent);
    }

    /**
     * Export rates grid to CSV format
     *
     * @return ResponseInterface
     */
    public function exportCsvAction()
    {
        $this->_view->loadLayout(false);
        $content = $this->_view->getLayout()->getChildBlock('adminhtml.tax.rate.grid', 'grid.export');
        return $this->_fileFactory->create('rates.csv', $content->getCsvFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }

    /**
     * Export rates grid to XML format
     *
     * @return ResponseInterface
     */
    public function exportXmlAction()
    {
        $this->_view->loadLayout(false);
        $content = $this->_view->getLayout()->getChildBlock('adminhtml.tax.rate.grid', 'grid.export');
        return $this->_fileFactory->create('rates.xml', $content->getExcelFile(), \Magento\Framework\App\Filesystem::VAR_DIR);
    }

    /**
     * Initialize action
     *
     * @return \Magento\Backend\App\Action
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_Tax::sales_tax_rates'
        )->_addBreadcrumb(
            __('Sales'),
            __('Sales')
        )->_addBreadcrumb(
            __('Tax'),
            __('Tax')
        );
        return $this;
    }

    /**
     * Import and export Page
     *
     * @return void
     */
    public function importExportAction()
    {
        $this->_title->add(__('Tax Zones and Rates'));

        $this->_title->add(__('Import and Export Tax Rates'));

        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_Tax::system_convert_tax'
        )->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Tax\Block\Adminhtml\Rate\ImportExportHeader')
        )->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Tax\Block\Adminhtml\Rate\ImportExport')
        );
        $this->_view->renderLayout();
    }

    /**
     * import action from import/export tax
     *
     * @return void
     */
    public function importPostAction()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_rates_file']['tmp_name'])) {
            try {
                /** @var $importHandler \Magento\Tax\Model\Rate\CsvImportHandler */
                $importHandler = $this->_objectManager->create('Magento\Tax\Model\Rate\CsvImportHandler');
                $importHandler->importFromCsvFile($this->getRequest()->getFiles('import_rates_file'));

                $this->messageManager->addSuccess(__('The tax rate has been imported.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Invalid file upload attempt'));
            }
        } else {
            $this->messageManager->addError(__('Invalid file upload attempt'));
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }

    /**
     * export action from import/export tax
     *
     * @return ResponseInterface
     */
    public function exportPostAction()
    {
        /** start csv content and set template */
        $headers = new \Magento\Framework\Object(
            array(
                'code' => __('Code'),
                'country_name' => __('Country'),
                'region_name' => __('State'),
                'tax_postcode' => __('Zip/Post Code'),
                'rate' => __('Rate'),
                'zip_is_range' => __('Zip/Post is Range'),
                'zip_from' => __('Range From'),
                'zip_to' => __('Range To')
            )
        );
        $template = '"{{code}}","{{country_name}}","{{region_name}}","{{tax_postcode}}","{{rate}}"' .
            ',"{{zip_is_range}}","{{zip_from}}","{{zip_to}}"';
        $content = $headers->toString($template);

        $storeTaxTitleTemplate = array();
        $taxCalculationRateTitleDict = array();

        foreach ($this->_objectManager->create(
            'Magento\Store\Model\Store'
        )->getCollection()->setLoadDefault(
            false
        ) as $store) {
            $storeTitle = 'title_' . $store->getId();
            $content .= ',"' . $store->getCode() . '"';
            $template .= ',"{{' . $storeTitle . '}}"';
            $storeTaxTitleTemplate[$storeTitle] = null;
        }
        unset($store);

        $content .= "\n";

        foreach ($this->_objectManager->create(
            'Magento\Tax\Model\Calculation\Rate\Title'
        )->getCollection() as $title) {
            $rateId = $title->getTaxCalculationRateId();

            if (!array_key_exists($rateId, $taxCalculationRateTitleDict)) {
                $taxCalculationRateTitleDict[$rateId] = $storeTaxTitleTemplate;
            }

            $taxCalculationRateTitleDict[$rateId]['title_' . $title->getStoreId()] = $title->getValue();
        }
        unset($title);

        $collection = $this->_objectManager->create(
            'Magento\Tax\Model\Resource\Calculation\Rate\Collection'
        )->joinCountryTable()->joinRegionTable();

        while ($rate = $collection->fetchItem()) {
            if ($rate->getTaxRegionId() == 0) {
                $rate->setRegionName('*');
            }

            if (array_key_exists($rate->getId(), $taxCalculationRateTitleDict)) {
                $rate->addData($taxCalculationRateTitleDict[$rate->getId()]);
            } else {
                $rate->addData($storeTaxTitleTemplate);
            }

            $content .= $rate->toString($template) . "\n";
        }
        $this->_view->loadLayout();
        return $this->_fileFactory->create('tax_rates.csv', $content, \Magento\Framework\App\Filesystem::VAR_DIR);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'importExport':
                return $this->_authorization->isAllowed('Magento_Tax::import_export');
                break;

            case 'index':
                return $this->_authorization->isAllowed('Magento_Tax::manage_tax');
                break;

            case 'importPost':
            case 'exportPost':
                return $this->_authorization->isAllowed(
                    'Magento_Tax::manage_tax'
                ) || $this->_authorization->isAllowed(
                    'Magento_Tax::import_export'
                );
                break;

            default:
                return $this->_authorization->isAllowed('Magento_Tax::manage_tax');
                break;
        }
    }

    /**
     * Populate a tax rate data object
     *
     * @param array $formData
     * @return \Magento\Tax\Service\V1\Data\TaxRate
     */
    protected function populateTaxRateData($formData)
    {
        $this->_taxRateBuilder->setId($this->extractFormData($formData, 'tax_calculation_rate_id'))
            ->setCountryId($this->extractFormData($formData, 'tax_country_id'))
            ->setRegionId($this->extractFormData($formData, 'tax_region_id'))
            ->setPostcode($this->extractFormData($formData, 'tax_postcode'))
            ->setCode($this->extractFormData($formData, 'code'))
            ->setPercentageRate($this->extractFormData($formData, 'rate'));

        if (isset($formData['zip_is_range']) && $formData['zip_is_range']) {
            $this->_zipRangeBuilder->setFrom($this->extractFormData($formData, 'zip_from'))
                ->setTo($this->extractFormData($formData, 'zip_to'));
            $zipRange = $this->_zipRangeBuilder->create();
            $this->_taxRateBuilder->setZipRange($zipRange);
        }

        if (isset($formData['title'])) {
            $titles = [];
            foreach ($formData['title'] as $storeId => $value) {
                $titles[] = $this->_taxRateTitleBuilder->setStoreId($storeId)->setValue($value)->create();
            }
            $this->_taxRateBuilder->setTitles($titles);
        }

        return $this->_taxRateBuilder->create();
    }

    /**
     * Determines if an array value is set in the form data array and returns it.
     *
     * @param array $formData the form to get data from
     * @param string $fieldName the key
     * @return null|string
     */
    protected function extractFormData($formData, $fieldName)
    {
        if (isset($formData[$fieldName])) {
            return $formData[$fieldName];
        }
        return null;
    }
}
