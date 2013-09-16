<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for authors
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Authors
    extends Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Abstract
{
    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Authors');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Authors');
    }

    /**
     * Return add author button html
     *
     * @return string
     */
    public function getAddAuthorButtonHtml()
    {
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setType('button')
            ->setClass('add')
            ->setLabel(__('Add Author'))
            ->setOnClick('addAuthor()')
            ->toHtml();
    }

    /**
     * Return array of authors
     *
     * @return array
     */
    public function getAuthors()
    {
        $authors = array();
        if ($this->getData('authors')) {
            $temp = array();
            foreach ($this->getData('authors') as $param => $values) {
                if (is_array($values)) {
                    foreach ($values as $key => $value) {
                        $temp[$key][$param] =$value;
                    }
                }
            }
            foreach ($temp as $key => $value) {
                $authors[$key] = $this->_coreData->jsonEncode($value);
            }
        }
        return $authors;
    }
}
