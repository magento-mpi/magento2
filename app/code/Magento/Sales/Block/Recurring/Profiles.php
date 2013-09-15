<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profiles listing
 */
namespace Magento\Sales\Block\Recurring;

class Profiles extends \Magento\Core\Block\Template
{
    /**
     * Profiles collection
     *
     * @var \Magento\Sales\Model\Resource\Recurring\Profile\Collection
     */
    protected $_profiles = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepare profiles collection and render it as grid information
     */
    public function prepareProfilesGrid()
    {
        $this->_prepareProfiles(array('reference_id', 'state', 'created_at', 'updated_at', 'method_code'));

        $pager = $this->getLayout()->createBlock('Magento\Page\Block\Html\Pager')
            ->setCollection($this->_profiles)->setIsOutputRequired(false);
        $this->setChild('pager', $pager);

        /* @var $profile \Magento\Sales\Model\Recurring\Profile */
        $profile = \Mage::getModel('Magento\Sales\Model\Recurring\Profile');

        $this->setGridColumns(array(
            new \Magento\Object(array(
                'index' => 'reference_id',
                'title' => $profile->getFieldLabel('reference_id'),
                'is_nobr' => true,
                'width' => 1,
            )),
            new \Magento\Object(array(
                'index' => 'state',
                'title' => $profile->getFieldLabel('state'),
            )),
            new \Magento\Object(array(
                'index' => 'created_at',
                'title' => $profile->getFieldLabel('created_at'),
                'is_nobr' => true,
                'width' => 1,
                'is_amount' => true,
            )),
            new \Magento\Object(array(
                'index' => 'updated_at',
                'title' => $profile->getFieldLabel('updated_at'),
                'is_nobr' => true,
                'width' => 1,
            )),
            new \Magento\Object(array(
                'index' => 'method_code',
                'title' => $profile->getFieldLabel('method_code'),
                'is_nobr' => true,
                'width' => 1,
            )),
        ));

        $profiles = array();
        $store = \Mage::app()->getStore();
        $locale = \Mage::app()->getLocale();
        foreach($this->_profiles as $profile) {
            $profile->setStore($store)->setLocale($locale);
            $profiles[] = new \Magento\Object(array(
                'reference_id' => $profile->getReferenceId(),
                'reference_id_link_url' => $this->getUrl('sales/recurring_profile/view/', array('profile' => $profile->getId())),
                'state'       => $profile->renderData('state'),
                'created_at'  => $this->formatDate($profile->getData('created_at'), 'medium', true),
                'updated_at'  => $profile->getData('updated_at') ? $this->formatDate($profile->getData('updated_at'), 'short', true) : '',
                'method_code' => $profile->renderData('method_code'),
            ));
        }
        if ($profiles) {
            $this->setGridElements($profiles);
        }
        $orders = array();
    }

    /**
     * Instantiate profiles collection
     *
     * @param array|int $fields
     */
    protected function _prepareProfiles($fields = '*')
    {
        $this->_profiles = \Mage::getModel('Magento\Sales\Model\Recurring\Profile')->getCollection()
            ->addFieldToFilter('customer_id', $this->_coreRegistry->registry('current_customer')->getId())
            ->addFieldToSelect($fields)
            ->setOrder('profile_id', 'desc')
        ;
    }

    /**
     * Set back Url
     *
     * @return \Magento\Sales\Block\Recurring\Profiles
     */
    protected function _beforeToHtml()
    {
        $this->setBackUrl($this->getUrl('customer/account/'));
        return parent::_beforeToHtml();
    }
}
