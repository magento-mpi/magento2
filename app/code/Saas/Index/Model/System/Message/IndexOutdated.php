<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Saas_Index_Model_System_Message_IndexOutdated extends  Mage_Index_Model_System_Message_IndexOutdated
{
    /**
     * @var Saas_Index_Model_Flag
     */
    protected $_flag;

    /**
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Index_Model_Indexer $indexer
     * @param Mage_Core_Model_UrlInterface $urlBuilder
     * @param Mage_Core_Model_Authorization $authorization
     * @param Saas_Index_Model_FlagFactory $flagFactory
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Index_Model_Indexer $indexer,
        Mage_Core_Model_UrlInterface $urlBuilder,
        Mage_Core_Model_Authorization $authorization,
        Saas_Index_Model_FlagFactory $flagFactory
    ) {
        $this->_flag = $flagFactory->create();
        $this->_flag->loadSelf();
        parent::__construct($helperFactory, $indexer, $urlBuilder, $authorization);
    }

    public function isDisplayed()
    {
        $state = $this->_flag->getState();
        return parent::isDisplayed() && ($state == Saas_Index_Model_Flag::STATE_NOTIFIED || is_null($state));
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $helper = $this->_helperFactory->get('Saas_Index_Helper_Data');
        $url = $this->_urlBuilder->getUrl('adminhtml/process/list');
        $text = $helper->__('You need to refresh the search index. Please click <a href="%s">here</a>.', $url);
        return $text;
    }
}
