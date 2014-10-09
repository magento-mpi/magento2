<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model\Layer\Search;

use Magento\Catalog\Model\Layer\Search\StateKeyInterface;

class StateKey extends \Magento\Catalog\Model\Layer\Category\StateKey implements StateKeyInterface
{
    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    protected $queryFactory;

    /**
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Search\Model\QueryFactory $queryFactory
     */
    public function __construct(
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Search\Model\QueryFactory $queryFactory
    ) {
        $this->queryFactory = $queryFactory;
        parent::__construct($storeManager, $customerSession);
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return string|void
     */
    public function toString($category)
    {
        return 'Q_' . $this->queryFactory->get()->getId()
        . '_' . \Magento\Catalog\Model\Layer\Category\StateKey::toString($category);
    }
}
