<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer account link
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Customer_Block_Account_Link extends Magento_Core_Block_Abstract
{
    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_session;

    /**
     * @param Magento_Core_Block_Context $context
     * @param Magento_Customer_Model_Session $session
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Context $context,
        Magento_Customer_Model_Session $session,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_session = $session;
    }

    /**
     * Add link to customer account page to the target block
     *
     * @param string $target
     * @param int $position
     * @return Magento_Customer_Block_Account_Link
     */
    public function addAccountLink($target, $position)
    {
        $helper = $this->_helperFactory->get('Magento_Customer_Helper_Data');
        $this->_addLink(
            $target, __('My Account'), $helper->getAccountUrl(), __('My Account'), $position, '', ''
        );
        return $this;
    }

    /**
     * Add link to customer registration page to the target block
     *
     * @param string $target
     * @param int $position
     * @param string $textBefore
     * @param string $textAfter
     * @return Magento_Customer_Block_Account_Link
     */
    public function addRegisterLink($target, $position, $textBefore = '', $textAfter = '')
    {

        if (!$this->_session->isLoggedIn()) {
            $helper = $this->_helperFactory->get('Magento_Customer_Helper_Data');
            $this->_addLink(
                $target,
                __('register'),
                $helper->getRegisterUrl(),
                __('register'),
                $position,
                $textBefore,
                $textAfter
            );
        }
        return $this;
    }

    /**
     * Remove link to customer registration page in the target block
     *
     * @param string $target
     * @return Magento_Customer_Block_Account_Link
     */
    public function removeRegisterLink($target)
    {
        $helper = $this->_helperFactory->get('Magento_Customer_Helper_Data');
        $this->_removeLink($target, $helper->getRegisterUrl());
        return $this;
    }

    /**
     * Add Log In link to the target block
     *
     * @param string $target
     * @param int $position
     * @return Magento_Customer_Block_Account_Link
     */
    public function addLogInLink($target, $position)
    {
        $helper = $this->_helperFactory->get('Magento_Customer_Helper_Data');
        if (!$this->_session->isLoggedIn()) {
            $this->_addLink(
                $target, __('Log In'), $helper->getLogoutUrl(), __('Log In'), $position, '', ''
            );
        }
        return $this;
    }

    /**
     * Add Log In/Out link to the target block
     *
     * @param string $target
     * @param int $position
     * @return Magento_Customer_Block_Account_Link
     */
    public function addAuthLink($target, $position)
    {
        $helper = $this->_helperFactory->get('Magento_Customer_Helper_Data');
        if ($this->_session->isLoggedIn()) {
            $this->_addLink(
                $target, __('Log Out'), $helper->getLogoutUrl(), __('Log Out'), $position, '', ''
            );
        } else {
            $this->_addLink(
                $target, __('Log In'), $helper->getLoginUrl(), __('Log In'), $position, '', ''
            );
        }
        return $this;
    }

    /**
     * Add link to the block with $target name
     *
     * @param string $target
     * @param string $text
     * @param string $url
     * @param string $title
     * @param int $position
     * @param string $textBefore
     * @param string $textAfter
     * @return Magento_Customer_Block_Account_Link
     */
    protected function _addLink($target, $text, $url, $title, $position, $textBefore='', $textAfter='')
    {
        /** @var $target Magento_Page_Block_Template_Links */
        $target = $this->getLayout()->getBlock($target);
        if ($target && method_exists($target, 'addLink')) {
            $target->addLink($text, $url, $title, true, array(), $position, null, null, $textBefore, $textAfter);
        }
        return $this;
    }

    /**
     * Remove Log In/Out link from the target block
     *
     * @param string $target
     * @return Magento_Customer_Block_Account_Link
     */
    public function removeAuthLink($target)
    {
        $helper = $this->_helperFactory->get('Magento_Customer_Helper_Data');
        if ($this->_session->isLoggedIn()) {
            $this->_removeLink($target, $helper->getLogoutUrl());
        } else {
            $this->_removeLink($target, $helper->getLoginUrl());
        }
        return $this;
    }

    /**
     * Remove link from the block with $target name
     *
     * @param string $target
     * @param string $url
     * @return Magento_Customer_Block_Account_Link
     */
    protected function _removeLink($target, $url)
    {
        /** @var $target Magento_Page_Block_Template_Links */
        $target = $this->getLayout()->getBlock($target);
        if ($target && method_exists($target, 'removeLinkByUrl')) {
            $target->removeLinkByUrl($url);
        }
        return $this;
    }
}
