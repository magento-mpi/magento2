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
 * Gift registry search results
 *
 * @category   Magento
 * @package    Magento_GiftRegistry
 */
class Magento_GiftRegistry_Block_Search_Results extends Magento_Core_Block_Template
{
    /**
     * Set search results and create html pager block
     */
    public function setSearchResults($results)
    {
        $this->setData('search_results', $results);
        $pager = $this->getLayout()->createBlock('Magento_Page_Block_Html_Pager', 'giftregistry.search.pager')
            ->setCollection($results)->setIsOutputRequired(false);
        $this->setChild('pager', $pager);
    }

    /**
     * Return frontend registry link
     *
     * @param Magento_GiftRegistry_Model_Entity $item
     * @return string
     */
    public function getRegistryLink($item)
    {
        return $this->getUrl('*/view/index', array('id' => $item->getUrlKey()));
    }

    /**
     * Retrieve item formated date
     *
     * @param Magento_GiftRegistry_Model_Entity $item
     * @return string
     */
    public function getFormattedDate($item)
    {
        if ($item->getEventDate()) {
            return $this->formatDate($item->getEventDate(), Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
        }
    }
}
