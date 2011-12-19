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
 * @package     Mage_OAuth
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Application model
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method Mage_OAuth_Model_Resource_Consumer _getResource()
 * @method Mage_OAuth_Model_Resource_Consumer getResource()
 * @method Mage_OAuth_Model_Resource_Consumer_Collection getCollection()
 * @method Mage_OAuth_Model_Resource_Consumer_Collection getResourceCollection()
 * @method string getName()
 * @method Mage_OAuth_Model_Consumer setName() setName(string $name)
 * @method string getKey()
 * @method Mage_OAuth_Model_Consumer setKey() setKey(string $key)
 * @method string getSecret()
 * @method Mage_OAuth_Model_Consumer setSecret() setSecret(string $secret)
 * @method string getCallBackUrl()
 * @method Mage_OAuth_Model_Consumer setCallBackUrl() setCallBackUrl(string $url)
 * @method string getCreatedAt()
 * @method Mage_OAuth_Model_Consumer setCreatedAt() setCreatedAt(string $date)
 * @method string getUpdatedAt()
 * @method Mage_OAuth_Model_Consumer setUpdatedAt() setUpdatedAt(string $date)
 */
class Mage_OAuth_Model_Consumer extends Mage_Core_Model_Abstract
{
    /**
     * Key hash length
     */
    const KEY_LENGTH = 32;

    /**
     * Secret hash length
     */
    const SECRET_LENGTH = 64;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('oauth/consumer');
    }

    /**
     * Update "updated at" date
     *
     * @return Mage_OAuth_Model_Consumer
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setUpdatedAt(time());
        }
        parent::_beforeSave();
        return $this;
    }

    /**
     * Validate data
     *
     * @return array|bool
     */
    public function validate()
    {
        $errors = array();
        if ($this->getCallBackUrl()) {
            /** @var $validatorUrl Mage_OAuth_Model_Consumer_Validator_Url */
            $validatorUrl = Mage::getSingleton('oauth/consumer_validator_url');
            if ($validatorUrl->isValid($this->getCallBackUrl())) {
                $errors = array_merge($errors, $validatorUrl->getMessages());
            }
        }
        $validatorLength = new Zend_Validate_StringLength();
        $validatorLength->setMin(self::KEY_LENGTH);
        $validatorLength->setMax(self::KEY_LENGTH);
        if ($validatorLength->isValid($this->getKey())) {
            $errors = $errors[] = sprintf(
                'Consumer key must has length %s symbols.', self::KEY_LENGTH);
        }
        $validatorLength->setMin(self::SECRET_LENGTH);
        $validatorLength->setMax(self::SECRET_LENGTH);
        if ($validatorLength->isValid($this->getSecret())) {
            $errors = $errors[] = sprintf(
                'Consumer key must has length %s symbols.', self::SECRET_LENGTH);
        }
        return $errors ? $errors : true;
    }
}
