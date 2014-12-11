<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Block\Search;

/**
 * Gift registry search results
 *
 */
class Results extends \Magento\Framework\View\Element\Template
{
    /**
     * Set search results and create html pager block
     *
     * @param mixed $results
     * @return void
     */
    public function setSearchResults($results)
    {
        $this->setData('search_results', $results);
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'giftregistry.search.pager'
        )->setCollection(
            $results
        )->setIsOutputRequired(
            false
        );
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
        return $this->getUrl('*/view/index', ['id' => $item->getUrlKey()]);
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
            return $this->formatDate(
                $item->getEventDate(),
                \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM
            );
        }
    }
}
