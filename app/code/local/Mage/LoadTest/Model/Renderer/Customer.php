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
 * @category   Mage
 * @package    Mage_Loadtest
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Renderer customer model
 *
 * @category   Mage
 * @package    Mage_Loadtest
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_LoadTest_Model_Renderer_Customer extends Mage_LoadTest_Model_Renderer_Abstract
{
    /**
     * US Popular male names
     *
     * @var array
     */
    protected $_maleNames;

    /**
     * US Popular female names
     *
     * @var array
     */
    protected $_femaleNames;

    /**
     * US Popular last names
     *
     * @var array
     */
    protected $_lastNames;

    /**
     * Postcode collection
     *
     * @var array
     */
    protected $_postCodes;

    /**
     * Store collection
     *
     * @var array
     */
    protected $_stores;

    /**
     * Customer data for profiler
     *
     * @var array
     */
    protected $_customer;

    /**
     * Init model
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setEmailMask('qa__%s@varien.com');
        $this->setPassword('123123');
        $this->setGroupId(1);
        $this->setCount(100);
    }

    /**
     * Render Castomers
     *
     * @return Mage_LoadTest_Model_Renderer_Customer
     */
    public function render()
    {
        $this->_profilerBegin();
        for ($i = 0; $i < $this->getCount(); $i++) {
            $this->_createCustomer();
        }
        $this->_profilerEnd();
        return $this;
    }

    /**
     * Delete All Customers
     *
     * @return Mage_LoadTest_Model_Renderer_Customer
     */
    public function delete()
    {
        $this->_profilerBegin();
        $collection = Mage::getModel('customer/customer')
            ->getCollection()
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email')
            ->load();

        foreach ($collection as $customer) {
            $this->_profilerOperationStart();
            $this->_customer = array(
                'id'        => $customer->getId(),
                'firstname' => $customer->getFirstname(),
                'lastname'  => $customer->getLastname(),
                'email'     => $customer->getEmail()
            );
            $customer->delete();
            $this->_profilerOperationStop();
        }
        $this->_profilerEnd();

        return $this;
    }

    /**
     * Create customer
     *
     * @return int
     */
    protected function _createCustomer()
    {
        $this->_loadData();

        $this->_profilerOperationStart();

        $customer = Mage::getModel('customer/customer');
        $address = Mage::getModel('customer/address');

        $customerInfo = $this->_getInfo();
        $customerAddress = $this->_getAddress();

        $customer->setStoreId(array_rand($this->_stores));
        $customer->setFirstname($customerInfo['firstname']);
        $customer->setLastname($customerInfo['lastname']);
        $customer->setEmail($customerInfo['email']);
        $customer->setGroupId($this->getGroupId());
        $customer->setPassword($customerInfo['password']);
        $customer->setDefaultBilling('_item1');
        $customer->setDefaultShipping('_item1');
        $customer->setCreatedIn(0);
        $customer->setIsSubscribed(false);

        $address->setFirstname($customerInfo['firstname']);
        $address->setLastname($customerInfo['lastname']);
        $address->setStreet($customerAddress['street']);
        $address->setCity($customerAddress['city']);
        $address->setCountryId($customerAddress['country_id']);
        $address->setRegionId($customerAddress['region_id']);
        $address->setPostcode($customerAddress['postcode']);
        $address->setTelephone($customerAddress['phone']);
        $address->setPostIndex('_item1');

        $customer->addAddress($address);

        $customer->save();
        $customer->setEmail(sprintf($this->getEmailMask(), $customer->getId()));
        $customer->save();

        $customerId = $customer->getId();

        $this->_customer = array(
            'id'        => $customerId,
            'firstname' => $customer->getFirstname(),
            'lastname'  => $customer->getLastname(),
            'email'     => $customer->getEmail()
        );

        unset($customer);

        $this->_profilerOperationStop();

        return $customerId;
    }

    /**
     * Get generated personal customer info
     *
     * @return array
     */
    protected function _getInfo()
    {
        $this->_loadData();

        if (rand(0, 3) != 1) {
            $firstName = trim($this->_maleNames[array_rand($this->_maleNames)]);
        }
        else {
            $firstName = trim($this->_femaleNames[array_rand($this->_femaleNames)]);
        }
        $lastName = trim($this->_lastNames[array_rand($this->_lastNames)]);

        $email = sprintf($this->getEmailMask(), strtolower($firstName . '_' . $lastName));

        return array(
            'firstname' => $firstName,
            'lastname'  => $lastName,
            'email'     => $email,
            'password'  => $this->getPassword()
        );
    }

    /**
     * Get generated customer address
     *
     * @return array
     */
    protected function _getAddress()
    {
        $this->_loadData();

        $address = $this->_postCodes[array_rand($this->_postCodes)];
        $address['street'] = rand(1,9). 'th St, ' . rand(1, 99);
        $address['phone'] = '('.rand(100, 910).') ' . rand(100, 999) . '-' . sprintf('%04d', rand(0, 9999));

        return $address;
    }

    /**
     * Load model data
     *
     */
    protected function _loadData()
    {
        if (is_null($this->_lastNames)) {
            $this->_maleNames   = file(BP . '/app/code/local/Mage/LoadTest/Data/NameMale.txt');
            $this->_femaleNames = file(BP . '/app/code/local/Mage/LoadTest/Data/NameFemale.txt');
            $this->_lastNames   = file(BP . '/app/code/local/Mage/LoadTest/Data/NameLast.txt');
        }
        if (is_null($this->_postCodes)) {
            $this->_postCodes = array();
            $collection = Mage::getModel('usa/postcode')
                ->getCollection();
            foreach ($collection as $item) {
                $this->_postCodes[] = array(
                    'country_id'    => $item->getCountryId(),
                    'postcode'      => $item->getPostcode(),
                    'region_id'     => $item->getRegionId(),
                    'city'          => ucwords(strtolower($item->getCity()))
                );
            }
            unset($collection);
        }
        if (is_null($this->_stores)) {
            $this->_stores = array();
            $collection = Mage::getModel('core/store')
                ->getCollection();
            foreach ($collection as $item) {
                $this->_stores[$item->getId()] = $item;
            }
            unset($collection);
        }
    }

    protected function _profilerOperationStop()
    {
        parent::_profilerOperationStop();

        if ($this->debug) {
            if (!$this->_xmlFieldSet) {
                $this->_xmlFieldSet = $this->_xmlResponse->addChild('customers');
            }

            $customer = $this->_xmlFieldSet->addChild('customer');
            $customer->addAttribute('id', $this->_customer['id']);
            $customer->addChild('firstname', $this->_customer['firstname']);
            $customer->addChild('lastname', $this->_customer['lastname']);
            $customer->addChild('email', $this->_customer['email']);
            $this->_profilerOperationAddDebugInfo($customer);
        }
    }
}