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
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paybox Api Debug Model
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @author     Ruslan Voitenko <ruslan.voytenko@varien.com>
 */
class Mage_Paybox_Model_Question_Number extends Mage_Core_Model_Abstract
{
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
        if (!$this->getAccountHash()) {
            $this->setAccountHash($this->_accountHash);
            $this->setIncrementValue(1);
            $this->save();
        }
        unset($this->_accountHash);

        if ($this->getIncrementValue() >= self::MAX_QUESTION_NUMBER_VALUE) {
            $this->setResetDate('CURRENT_TIMESTAMP')
                ->setIncrementValue(1);
        }

        return parent::_afterLoad();
    }

    public function getNextQuestionNumber()
    {
        return $this->getIncrementValue()+1;
    }

    public function increaseQuestionNumber()
    {
        $this->setIncrementValue($this->getIncrementValue()+1)
            ->save();
        return $this;
    }
}