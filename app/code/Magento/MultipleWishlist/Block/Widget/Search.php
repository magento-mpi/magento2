<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist Search Widget Block
 */
class Magento_MultipleWishlist_Block_Widget_Search extends Magento_Core_Block_Template 
    implements Magento_Widget_Block_Interface
{
    /**
     * Search form select options
     *
     * @var array
     */
    protected $_selectOptions;

    /**
     * Config source search model
     *
     * @var Magento_MultipleWishlist_Model_Config_Source_Search
     */
    protected $_configSourceSearch;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_MultipleWishlist_Model_Config_Source_Search $configSourceSearch
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_MultipleWishlist_Model_Config_Source_Search $configSourceSearch,
        array $data = array()
    ) {
        $this->_configSourceSearch = $configSourceSearch;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve form types list
     *
     * @return array
     */
    protected function _getEnabledFormTypes()
    {
        $types = $this->_getData('types');
        if (is_array($types)) {
            return $types;
        }
        if (empty($types)) {
            $types = array();
        } else {
            $types = explode(',', $types);
        }
        $this->setData('types', $types);

        return $types;
    }

    /**
     * Check whether specified form must be available as part of quick search form
     *
     * @param string $code
     * @return bool
     */
    protected function _checkForm($code)
    {
        return in_array($code, $this->_getEnabledFormTypes());
    }

    /**
     * Check if all quick search forms must be used
     *
     * @return bool
     */
    public function useAllForms()
    {
        $code = Magento_MultipleWishlist_Model_Config_Source_Search::WISHLIST_SEARCH_DISPLAY_ALL_FORMS;
        return $this->_checkForm($code);
    }

    /**
     * Check if name quick search form must be used
     *
     * @return bool
     */
    public function useNameForm()
    {
        $code = Magento_MultipleWishlist_Model_Config_Source_Search::WISHLIST_SEARCH_DISPLAY_NAME_FORM;
        return $this->useAllForms() || $this->_checkForm($code);
    }

    /**
     * Check if email quick search form must be used
     *
     * @return string
     */
    public function useEmailForm()
    {
        $code = Magento_MultipleWishlist_Model_Config_Source_Search::WISHLIST_SEARCH_DISPLAY_EMAIL_FORM;
        return $this->useAllForms() || $this->_checkForm($code);
    }

    /**
     * Retrieve HTML for search form select
     *
     * @return string
     */
    public function getSearchFormSelect()
    {
        $options = array_merge(array(
            array(
                'value' => '',
                'label' => __('Select Search Type'))
            ),
            $this->getSearchFormOptions()
        );

        $select = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setName('search_by')
            ->setId($this->getBlockId() . '-search_by')
            ->setOptions($options);

        return $select->getHtml();
    }

    /**
     * Add current block identifier to dom node id
     *
     * @return string
     */
    public function getBlockId()
    {
        if ($this->getData('id') === null) {
            $this->setData('id', $this->_coreData->uniqHash());
        }
        return $this->getData('id');
    }

    /**
     * Retrieve options for search form select
     *
     * @return array
     */
    public function getSearchFormOptions()
    {
        if (is_null($this->_selectOptions)) {
            $allForms = $this->_configSourceSearch->getTypes();
            $useForms = $this->_getEnabledFormTypes();
            $codeAll = Magento_MultipleWishlist_Model_Config_Source_Search::WISHLIST_SEARCH_DISPLAY_ALL_FORMS;

            if (in_array($codeAll, $useForms)) {
                unset($allForms[$codeAll]);
            } else {
                 foreach ($allForms as $type => $label) {
                     if (!in_array($type, $useForms)) {
                         unset($allForms[$type]);
                    }
                }
            }
            $options = array();
            foreach ($allForms as $type => $label) {
                $options[] = array(
                    'value' => $type,
                    'label' => $label
                );
            }
            $this->_selectOptions = $options;
        }
        return $this->_selectOptions;
    }

    /**
     * Use search form select in quick search form
     *
     * @return bool
     */
    public function useSearchFormSelect()
    {
        return count($this->getSearchFormOptions()) > 1;
    }

    /**
     * Retrieve Multiple Search URL
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('wishlist/search/results');
    }
}
