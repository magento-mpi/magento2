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
 * Gift registry quick search widget block
 *
 * @category   Magento
 * @package    Magento_GiftRegistry
 */
class Magento_GiftRegistry_Block_Search_Widget_Form
    extends Magento_GiftRegistry_Block_Search_Quick
    implements Magento_Widget_Block_Interface
{
    /**
     * Search form select options
     */
    protected $_selectOptions;

    /**
     * Make form types getter always return array
     *
     * @return array
     */
    protected function _getFormTypes()
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
     * Check if specified form must be available as part of quick search form
     *
     * @return bool
     */
    protected function _checkForm($code)
    {
        return in_array($code, $this->_getFormTypes());
    }

    /**
     * Check if all quick search forms must be used
     *
     * @return bool
     */
    public function useAllForms()
    {
        $code = Magento_GiftRegistry_Model_Source_Search::SEARCH_ALL_FORM;
        return $this->_checkForm($code);
    }

    /**
     * Check if name quick search form must be used
     *
     * @return bool
     */
    public function useNameForm()
    {
        $code = Magento_GiftRegistry_Model_Source_Search::SEARCH_NAME_FORM;
        return $this->useAllForms() || $this->_checkForm($code);
    }

    /**
     * Check if email quick search form must be used
     *
     * @return string
     */
    public function useEmailForm()
    {
        $code = Magento_GiftRegistry_Model_Source_Search::SEARCH_EMAIL_FORM;
        return $this->useAllForms() || $this->_checkForm($code);
    }

    /**
     * Check if id quick search form must be used
     *
     * @return bool
     */
    public function useIdForm()
    {
        $code = Magento_GiftRegistry_Model_Source_Search::SEARCH_ID_FORM;
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
            ->setId('search-by')
            ->setOptions($options);

        return $select->getHtml();
    }

    /**
     * Retrieve options for search form select
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getSearchFormOptions()
    {
        if (is_null($this->_selectOptions)) {
            $allForms = Mage::getSingleton('Magento_GiftRegistry_Model_Source_Search')->getTypes();
            $useForms = $this->_getFormTypes();
            $codeAll = Magento_GiftRegistry_Model_Source_Search::SEARCH_ALL_FORM;

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
     * @return array
     */
    public function useSearchFormSelect()
    {
        return count($this->getSearchFormOptions()) > 1;
    }
}
