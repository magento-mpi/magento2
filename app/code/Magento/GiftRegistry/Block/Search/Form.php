<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Search;

/**
 * Gift registry search form
 *
 */
class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var mixed
     */
    protected $_formData = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\GiftRegistry\Model\TypeFactory
     */
    protected $typeFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\GiftRegistry\Model\TypeFactory $typeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\GiftRegistry\Model\TypeFactory $typeFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->typeFactory = $typeFactory;
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve form header
     *
     * @return string
     */
    public function getFormHeader()
    {
        return __('Gift Registry Search');
    }

    /**
     * Retrieve by key saved in session form data
     *
     * @param string $key
     * @return string|null
     */
    public function getFormData($key)
    {
        if (is_null($this->_formData)) {
            $this->_formData = $this->customerSession->getRegistrySearchData();
        }
        if (!$this->_formData || !isset($this->_formData[$key])) {
            return null;
        }
        return $this->escapeHtml($this->_formData[$key]);
    }

    /**
     * Return available gift registry types collection
     *
     * @return \Magento\GiftRegistry\Model\Resource\Type\Collection
     */
    public function getTypesCollection()
    {
        return $this->typeFactory->create()->getCollection()->addStoreData($this->_storeManager->getStore()->getId());
    }

    /**
     * Select element for choosing registry type
     *
     * @return string
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            ['id' => 'params-type-id', 'class' => 'select']
        )->setName(
            'params[type_id]'
        )->setOptions(
            $this->getTypesCollection()->toOptionArray(true)
        );
        return $select->getHtml();
    }

    /**
     * Return search form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('giftregistry/search/results');
    }

    /**
     * Return search form action url
     *
     * @return string
     */
    public function getAdvancedUrl()
    {
        return $this->getUrl('giftregistry/search/advanced');
    }
}
