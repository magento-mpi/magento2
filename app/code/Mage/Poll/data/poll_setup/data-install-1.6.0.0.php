<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */


/* @var $installer Magento_Core_Model_Resource_Setup */

$installer = $this;

$pollModel = Mage::getModel('Mage_Poll_Model_Poll');

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
    $answerModel = Mage::getModel('Mage_Poll_Model_Poll_Answer');
    $answerModel->setAnswerTitle($answer[0])
                ->setVotesCount($answer[1]);

    $pollModel->addAnswer($answerModel);
}

$pollModel->save();

