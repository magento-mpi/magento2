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

namespace Magento\Customer\Block\Account;

class Link extends \Magento\Core\Block\AbstractBlock
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @param \Magento\Core\Block\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Context $context,
        \Magento\Customer\Model\Session $session,
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
     * @return \Magento\Customer\Block\Account\Link
     */
    public function addAccountLink($target, $position)
    {
        $helper = $this->_helperFactory->get('Magento\Customer\Helper\Data');
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
     * @return \Magento\Customer\Block\Account\Link
     */
    public function addRegisterLink($target, $position, $textBefore = '', $textAfter = '')
    {

        if (!$this->_session->isLoggedIn()) {
            $helper = $this->_helperFactory->get('Magento\Customer\Helper\Data');
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
     * @return \Magento\Customer\Block\Account\Link
     */
    public function removeRegisterLink($target)
    {
        $helper = $this->_helperFactory->get('Magento\Customer\Helper\Data');
        $this->_removeLink($target, $helper->getRegisterUrl());
        return $this;
    }

    /**
     * Add Log In link to the target block
     *
     * @param string $target
     * @param int $position
     * @return \Magento\Customer\Block\Account\Link
     */
    public function addLogInLink($target, $position)
    {
        $helper = $this->_helperFactory->get('Magento\Customer\Helper\Data');
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
     * @return \Magento\Customer\Block\Account\Link
     */
    public function addAuthLink($target, $position)
    {
        $helper = $this->_helperFactory->get('Magento\Customer\Helper\Data');
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
     * @return \Magento\Customer\Block\Account\Link
     */
    protected function _addLink($target, $text, $url, $title, $position, $textBefore='', $textAfter='')
    {
        /** @var $target \Magento\Page\Block\Template\Links */
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
     * @return \Magento\Customer\Block\Account\Link
     */
    public function removeAuthLink($target)
    {
        $helper = $this->_helperFactory->get('Magento\Customer\Helper\Data');
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
     * @return \Magento\Customer\Block\Account\Link
     */
    protected function _removeLink($target, $url)
    {
        /** @var $target \Magento\Page\Block\Template\Links */
        $target = $this->getLayout()->getBlock($target);
        if ($target && method_exists($target, 'removeLinkByUrl')) {
            $target->removeLinkByUrl($url);
        }
        return $this;
    }
}
