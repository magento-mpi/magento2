<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Recurring\Profile;

/**
 * Recurring profile view grid
 */
class Grid extends \Magento\Sales\Block\Recurring\Profiles
{
    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\RecurringProfile\Model\Profile
     */
    protected $_recurringProfile;

    /**
     * Profiles collection
     *
     * @var \Magento\RecurringProfile\Model\Resource\Profile\Collection
     */
    protected $_profiles = null;

    /**
     * @var \Magento\RecurringProfile\Block\Fields
     */
    protected $_fields;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\RecurringProfile\Model\Profile $recurringProfile
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\RecurringProfile\Block\Fields $fields
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\RecurringProfile\Model\Profile $recurringProfile,
        \Magento\Core\Model\Registry $registry,
        \Magento\RecurringProfile\Block\Fields $fields,
        array $data = array()
    ) {
        $this->_recurringProfile = $recurringProfile;
        $this->_registry = $registry;
        parent::__construct($context, $data);
        $this->_fields = $fields;
        $this->_isScopePrivate = true;
    }

    /**
     * Instantiate profiles collection
     *
     * @param array|int|string $fields
     */
    protected function _prepareProfiles($fields = '*')
    {
        $this->_profiles = $this->_recurringProfile->getCollection()
            ->addFieldToFilter('customer_id', $this->_registry->registry('current_customer')->getId())
            ->addFieldToSelect($fields)
            ->setOrder('profile_id', 'desc');
    }

    /**
     * Prepare grid data
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_prepareProfiles(array('reference_id', 'state', 'created_at', 'updated_at', 'method_code'));

        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager')
            ->setCollection($this->_profiles)->setIsOutputRequired(false);
        $this->setChild('pager', $pager);

        $this->setGridColumns(array(
            new \Magento\Object(array(
                'index' => 'reference_id',
                'title' => $this->_fields->getFieldLabel('reference_id'),
                'is_nobr' => true,
                'width' => 1,
            )),
            new \Magento\Object(array(
                'index' => 'state',
                'title' => $this->_fields->getFieldLabel('state'),
            )),
            new \Magento\Object(array(
                'index' => 'created_at',
                'title' => $this->_fields->getFieldLabel('created_at'),
                'is_nobr' => true,
                'width' => 1,
                'is_amount' => true,
            )),
            new \Magento\Object(array(
                'index' => 'updated_at',
                'title' => $this->_fields->getFieldLabel('updated_at'),
                'is_nobr' => true,
                'width' => 1,
            )),
            new \Magento\Object(array(
                'index' => 'method_code',
                'title' => $this->_fields->getFieldLabel('method_code'),
                'is_nobr' => true,
                'width' => 1,
            )),
        ));

        $profiles = array();
        $store = $this->_storeManager->getStore();
        foreach ($this->_profiles as $profile) {
            $profile->setStore($store);
            $profiles[] = new \Magento\Object(array(
                'reference_id' => $profile->getReferenceId(),
                'reference_id_link_url' => $this->getUrl(
                    'sales/recurring_profile/view/',
                    array('profile' => $profile->getId())
                ),
                'state'       => $profile->renderData('state'),
                'created_at'  => $this->formatDate($profile->getData('created_at'), 'medium', true),
                'updated_at'  => $profile->getData('updated_at')
                    ? $this->formatDate($profile->getData('updated_at'), 'short', true)
                    : '',
                'method_code' => $profile->renderData('method_code'),
            ));
        }
        if ($profiles) {
            $this->setGridElements($profiles);
        }
    }
}
