<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;

/**
 * Customer giftregistry list block
 */
class ListCustomer extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\GiftRegistry\Model\EntityFactory
     */
    protected $entityFactory;

    /**
     * @var \Magento\GiftRegistry\Model\TypeFactory
     */
    protected $typeFactory;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerAddressServiceInterface $addressService
     * @param \Magento\GiftRegistry\Model\EntityFactory $entityFactory
     * @param \Magento\GiftRegistry\Model\TypeFactory $typeFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerAddressServiceInterface $addressService,
        \Magento\GiftRegistry\Model\EntityFactory $entityFactory,
        \Magento\GiftRegistry\Model\TypeFactory $typeFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = array()
    ) {
        $this->customerSession = $customerSession;
        $this->entityFactory = $entityFactory;
        $this->typeFactory = $typeFactory;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $addressService,
            $data
        );
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filterManager->truncate(
            $value,
            array('length' => $length, 'etc' => $etc, 'remainder' => $remainder, 'breakWords' => $breakWords)
        );
    }

    /**
     * Instantiate pagination
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'giftregistry.list.pager'
        )->setCollection(
            $this->getEntityCollection()
        )->setIsOutputRequired(
            false
        );
        $this->setChild('pager', $pager);
        return parent::_prepareLayout();
    }

    /**
     * Return list of gift registries
     *
     * @return \Magento\GiftRegistry\Model\Resource\GiftRegistry\Collection
     */
    public function getEntityCollection()
    {
        if (!$this->hasEntityCollection()) {
            $this->setData(
                'entity_collection',
                $this->entityFactory->create()->getCollection()
                ->filterByCustomerId($this->currentCustomer->getCustomerId())
            );
        }
        return $this->_getData('entity_collection');
    }

    /**
     * Check exist listed gift registry types on the current store
     *
     * @return bool
     */
    public function canAddNewEntity()
    {
        $collection = $this->typeFactory->create()
            ->getCollection()
            ->addStoreData($this->_storeManager->getStore()->getId())
            ->applyListedFilter();

        return (bool)$collection->getSize();
    }

    /**
     * Return add button form url
     *
     * @return string
     */
    public function getAddUrl()
    {
        return $this->getUrl('giftregistry/index/addselect');
    }

    /**
     * Return view entity items url
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getItemsUrl($item)
    {
        return $this->getUrl('giftregistry/index/items', array('id' => $item->getEntityId()));
    }

    /**
     * Return share entity url
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getShareUrl($item)
    {
        return $this->getUrl('giftregistry/index/share', array('id' => $item->getEntityId()));
    }

    /**
     * Return edit entity url
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getEditUrl($item)
    {
        return $this->getUrl('giftregistry/index/edit', array('entity_id' => $item->getEntityId()));
    }

    /**
     * Return delete entity url
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getDeleteUrl($item)
    {
        return $this->getUrl('giftregistry/index/delete', array('id' => $item->getEntityId()));
    }

    /**
     * Retrieve item title
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getEscapedTitle($item)
    {
        return $this->escapeHtml($item->getData('title'));
    }

    /**
     * Retrieve item formatted date
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getFormattedDate($item)
    {
        return $this->formatDate(
            $this->_localeDate->date(strtotime($item->getCreatedAt()), null, null, false),
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM
        );
    }

    /**
     * Retrieve escaped item message
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getEscapedMessage($item)
    {
        return $this->escapeHtml($item->getData('message'));
    }

    /**
     * Retrieve item message
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getIsActive($item)
    {
        return $item->getData('is_active');
    }
}
