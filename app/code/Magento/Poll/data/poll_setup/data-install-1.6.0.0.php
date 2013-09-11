<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */


/* @var $installer \Magento\Core\Model\Resource\Setup */

$installer = $this;

$pollModel = \Mage::getModel('\Magento\Poll\Model\Poll');

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
    $answerModel = \Mage::getModel('\Magento\Poll\Model\Poll\Answer');
    $answerModel->setAnswerTitle($answer[0])
                ->setVotesCount($answer[1]);

    $pollModel->addAnswer($answerModel);
}

$pollModel->save();

