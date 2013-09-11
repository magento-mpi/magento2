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
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block\Widget;

class Search extends \Magento\Core\Block\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Search form select options
     *
     * @var array
     */
    protected $_selectOptions;

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
        $code = \Magento\MultipleWishlist\Model\Config\Source\Search::WISHLIST_SEARCH_DISPLAY_ALL_FORMS;
        return $this->_checkForm($code);
    }

    /**
     * Check if name quick search form must be used
     *
     * @return bool
     */
    public function useNameForm()
    {
        $code = \Magento\MultipleWishlist\Model\Config\Source\Search::WISHLIST_SEARCH_DISPLAY_NAME_FORM;
        return $this->useAllForms() || $this->_checkForm($code);
    }

    /**
     * Check if email quick search form must be used
     *
     * @return string
     */
    public function useEmailForm()
    {
        $code = \Magento\MultipleWishlist\Model\Config\Source\Search::WISHLIST_SEARCH_DISPLAY_EMAIL_FORM;
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

        $select = $this->getLayout()->createBlock('\Magento\Core\Block\Html\Select')
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
            $this->setData('id', \Mage::helper('Magento\Core\Helper\Data')->uniqHash());
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
            $allForms = \Mage::getSingleton('Magento\MultipleWishlist\Model\Config\Source\Search')->getTypes();
            $useForms = $this->_getEnabledFormTypes();
            $codeAll = \Magento\MultipleWishlist\Model\Config\Source\Search::WISHLIST_SEARCH_DISPLAY_ALL_FORMS;

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
