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
 * @package    Mage_Poll
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll answers model
 *
 * @category   Mage
 * @package    Mage_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Poll_Model_Poll_Answer extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('poll/poll_answer');
    }

    public function countPercent($poll)
    {
        $this->setPercent(round(( $poll->getVotesCount() > 0 ) ? ($this->getVotesCount() * 100 / $poll->getVotesCount()) : 0));
        return $this;
    }

    protected function _afterSave()
    {
        Mage::getModel('poll/poll')
            ->setId($this->getPollId())
            ->resetVotesCount();
    }

    protected function _beforeDelete()
    {
        $this->setPollId($this->load($this->getId())->getPollId());
    }

    protected function _afterDelete()
    {
        Mage::getModel('poll/poll')
            ->setId($this->getPollId())
            ->resetVotesCount();
    }
}