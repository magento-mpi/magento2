<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry frontend search controller
 */
class Magento_GiftRegistry_Controller_Search extends Magento_Core_Controller_Front_Action
{
    /**
     * Check if gift registry is enabled on current store before all other actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::helper('Magento_GiftRegistry_Helper_Data')->isEnabled()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
    }

    /**
     * Get current customer session
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session');
    }

    /**
     * Initialize gift registry type model
     *
     * @param int $typeId
     * @return Magento_GiftRegistry_Model_Type
     */
    protected function _initType($typeId)
    {
        $type = Mage::getModel('Magento_GiftRegistry_Model_Type')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($typeId);

        Mage::register('current_giftregistry_type', $type);
        return $type;
    }

    /**
     * Filter input form data
     *
     * @param  array $params
     * @return array
     */
    protected function _filterInputParams($params)
    {
        foreach ($params as $key => $value) {
            $params[$key] = htmlspecialchars($value);
        }
        if (isset($params['type_id'])) {
            $type = $this->_initType($params['type_id']);
            $dateType = Mage::getSingleton('Magento_GiftRegistry_Model_Attribute_Config')->getStaticDateType();
            if ($dateType) {
                $attribute = $type->getAttributeByCode($dateType);
                $format = (isset($attribute['date_format'])) ? $attribute['date_format'] : null;

                $dateFields = array();
                $fromDate = $dateType . '_from';
                $toDate = $dateType . '_to';

                if (isset($params[$fromDate])) {
                    $dateFields[] = $fromDate;
                }
                if (isset($params[$toDate])) {
                    $dateFields[] = $toDate;
                }
                $params = $this->_filterInputDates($params, $dateFields, $format);
            }
        }
        return $params;
    }

    /**
     * Validate input search params
     *
     * @param array $params
     * @return bool
     */
    protected function _validateSearchParams($params)
    {
        if (empty($params) || !is_array($params) || empty($params['search'])) {
            $this->_getSession()->addNotice(
                __('Please enter correct search options.')
            );
            return false;
        }

        switch ($params['search']) {
            case 'type':
                if (empty($params['firstname']) || strlen($params['firstname']) < 2) {
                    $this->_getSession()->addNotice(
                        __('Please enter at least 2 letters of the first name.')
                    );
                    return false;
                }
                if (empty($params['lastname']) || strlen($params['lastname']) < 2) {
                    $this->_getSession()->addNotice(
                        __('Please enter at least 2 letters of the last name.')
                    );
                    return false;
                }
                break;

            case 'email':
                if (empty($params['email']) || !Zend_Validate::is($params['email'], 'EmailAddress')) {
                    $this->_getSession()->addNotice(
                        __('Please enter a valid email address.')
                    );
                    return false;
                }
                break;

            case 'id':
                if (empty($params['id'])) {
                    $this->_getSession()->addNotice(
                        __('Please enter a gift registry ID.')
                    );
                    return false;
                }
                break;

            default:
                $this->_getSession()->addNotice(
                    __('Please enter correct search options.')
                );
                return false;
        }
        return true;
    }

    /**
     * Convert dates in array from localized to internal format
     *
     * @param   array $array
     * @param   array $dateFields
     * @return  array
     */
    protected function _filterInputDates($array, $dateFields, $format = null)
    {
        if (empty($dateFields)) {
            return $array;
        }
        if (is_null($format)) {
            $format = Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT;
        }

        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'locale' => Mage::app()->getLocale()->getLocaleCode(),
            'date_format' => Mage::app()->getLocale()->getDateFormat($format)
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => Magento_Date::DATE_INTERNAL_FORMAT
        ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Gift Registry Search'));
        }
        $this->renderLayout();
    }

    /**
     * Index action
     */
    public function resultsAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');

        if ($params = $this->getRequest()->getParam('params')) {
            $this->_getSession()->setRegistrySearchData($params);
        } else {
            $params = $this->_getSession()->getRegistrySearchData();
        }

        if ($this->_validateSearchParams($params)) {
            $results = Mage::getModel('Magento_GiftRegistry_Model_Entity')->getCollection()
                ->applySearchFilters($this->_filterInputParams($params));

            $this->getLayout()->getBlock('giftregistry.search.results')
                ->setSearchResults($results);
        } else {
            $this->_redirect('*/*/index', array('_current' => true));
            return;
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Gift Registry Search'));
        }
        $this->renderLayout();
    }

    /**
     * Load type specific advanced search attributes
     */
    public function advancedAction()
    {
        $this->_initType($this->getRequest()->getParam('type_id'));
        $this->loadLayout();
        $this->renderLayout();
    }
}
