<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Filter item model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Layer_Filter_Item extends Magento_Object
{
    /**
     * Url
     *
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_url;

    /**
     * Html pager block
     *
     * @var Magento_Page_Block_Html_Pager
     */
    protected $_htmlPagerBlock;

    /**
     * Construct
     *
     * @param Magento_Core_Model_UrlInterface $url
     * @param Magento_Page_Block_Html_Pager $htmlPagerBlock
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_UrlInterface $url,
        Magento_Page_Block_Html_Pager $htmlPagerBlock,
        array $data = array()
    ) {
        $this->_url = $url;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        parent::__construct($data);
    }

    /**
     * Get filter instance
     *
     * @return Magento_Catalog_Model_Layer_Filter_Abstract
     * @throws Magento_Core_Exception
     */
    public function getFilter()
    {
        $filter = $this->getData('filter');
        if (!is_object($filter)) {
            throw new Magento_Core_Exception(
                __('The filter must be an object. Please set correct filter.')
            );
        }
        return $filter;
    }

    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        $query = array(
            $this->getFilter()->getRequestVar()=>$this->getValue(),
            $this->_htmlPagerBlock->getPageVarName() => null // exclude current page from urls
        );
        return $this->_url->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        $query = array($this->getFilter()->getRequestVar()=>$this->getFilter()->getResetValue());
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = true;
        return $this->_url->getUrl('*/*/*', $params);
    }

    /**
     * Get url for "clear" link
     *
     * @return false|string
     */
    public function getClearLinkUrl()
    {
        $clearLinkText = $this->getFilter()->getClearLinkText();
        if (!$clearLinkText) {
            return false;
        }

        $urlParams = array(
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => array($this->getFilter()->getRequestVar() => null),
            '_escape' => true,
        );
        return $this->_url->getUrl('*/*/*', $urlParams);
    }

    /**
     * Get item filter name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getFilter()->getName();
    }

    /**
     * Get item value as string
     *
     * @return string
     */
    public function getValueString()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            return implode(',', $value);
        }
        return $value;
    }
}
