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
 * @package    Mage_Paybox
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paybox Question Number Model
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paybox_Model_Question_Number extends Mage_Core_Model_Abstract
{
    /**
     * Max value of question number
     */
    const MAX_QUESTION_NUMBER_VALUE = 2147483647;

    protected $_accountHash;

    protected function _construct()
    {
        $this->_init('paybox/question_number');
    }

    public function load($id, $field=null)
    {
        $this->_accountHash = $id;
        return parent::load($id, $field);
    }

    protected function _afterLoad()
    {
        //need to create new record (with default data) if it first time using of paybox direct
        if (!$this->getAccountHash()) {
            $this->setAccountHash($this->_accountHash);
            $this->setIncrementValue(1);
            $this->save();
        }
        unset($this->_accountHash);

        //need to set default value of question number if it reach max value
        if ($this->getIncrementValue() >= self::MAX_QUESTION_NUMBER_VALUE) {
            $this->setResetDate('CURRENT_TIMESTAMP')
                ->setIncrementValue(1);
        }

        return parent::_afterLoad();
    }

    /**
     * Return next number formated to paybox specification
     *
     * @return string
     */
    public function getNextQuestionNumber()
    {
        $questionNumber = $this->getIncrementValue()+1;
        return sprintf('%010d', $questionNumber);
    }

    /**
     * Increase question number and save it after successful transaction
     *
     * @return Mage_Paybox_Model_Question_Number
     */
    public function increaseQuestionNumber()
    {
        $this->setIncrementValue($this->getIncrementValue()+1)
            ->save();
        return $this;
    }
}