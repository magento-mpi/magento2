<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */


/* @var $installer Magento_Core_Model_Resource_Setup */

$installer = $this;

$pollModel = Mage::getModel('Magento_Poll_Model_Poll');

$pollModel  ->setDatePosted(now())
            ->setPollTitle('What is your favorite color')
            ->setStoreIds(array(1));

$answers  = array(
                array('Green', 4),
                array('Red', 1),
                array('Black', 0),
                array('Magenta', 2)
                );

foreach( $answers as $key => $answer ) {
    $answerModel = Mage::getModel('Magento_Poll_Model_Poll_Answer');
    $answerModel->setAnswerTitle($answer[0])
                ->setVotesCount($answer[1]);

    $pollModel->addAnswer($answerModel);
}

$pollModel->save();

