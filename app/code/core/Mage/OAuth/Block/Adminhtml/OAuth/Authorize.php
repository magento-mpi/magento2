<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth consumers grid container block
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Block_Adminhtml_OAuth_Authorize extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve admin form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('adminhtml/index/login');
    }

    /**
     * Get consumer instance by token value
     *
     * @return Mage_OAuth_Model_Consumer
     */
    public function getConsumer()
    {
        /** @var $token Mage_OAuth_Model_Token */
        $token = Mage::getModel('oauth/token');
        $token->load($this->getOauthToken(), 'token');

        /** @var $consumer Mage_OAuth_Model_Consumer */
        $consumer = Mage::getModel('oauth/consumer');
        $consumer->load($token->getConsumerId());

        return $consumer;
    }

    /**
     * Has an exception been registered with the response?
     *
     * @return bool
     */
    public function isException()
    {
        return $this->getIsException();
    }
}
