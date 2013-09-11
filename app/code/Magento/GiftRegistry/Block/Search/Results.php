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
namespace Magento\GiftRegistry\Block\Search;

class Results extends \Magento\Core\Block\Template
{
    /**
     * Set search results and create html pager block
     */
    public function setSearchResults($results)
    {
        $this->setData('search_results', $results);
        $pager = $this->getLayout()->createBlock('Magento\Page\Block\Html\Pager', 'giftregistry.search.pager')
            ->setCollection($results)->setIsOutputRequired(false);
        $this->setChild('pager', $pager);
    }

    /**
     * Return frontend registry link
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getRegistryLink($item)
    {
        return $this->getUrl('*/view/index', array('id' => $item->getUrlKey()));
    }

    /**
     * Retrieve item formated date
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getFormattedDate($item)
    {
        if ($item->getEventDate()) {
            return $this->formatDate($item->getEventDate(), \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM);
        }
    }
}
