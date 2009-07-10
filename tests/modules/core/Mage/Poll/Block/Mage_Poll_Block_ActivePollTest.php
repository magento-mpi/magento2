<?php

class Mage_Poll_Block_ActivePollTest__Block_ActivePoll extends Mage_Poll_Block_ActivePoll
{
    public function getPollTemplate($type)
    {
        return $this->_templates[$type];
    }
}

class Mage_Poll_Block_ActivePollTest extends Mage_TestCase
{

    /**
     * @TODO Implement test for code in constructor after refactoring it
     */
    public function testConstructor()
    {
        $this->markTestIncomplete("testConstructor test not implemented");
    }

    public function testSetPollTemplate()
    {
        $block = new Mage_Poll_Block_ActivePollTest__Block_ActivePoll();
        $testTemplate = 'TEST_TEMPLATE';
        $testType = 'TEST_TYPE';
        $block->setPollTemplate($testTemplate, $testType);
        $this->assertEquals($testTemplate, $block->getPollTemplate($testType));
    }

    /**
     * @TODO move template selection logic into a separate method
     *       to simplify tests, because now we rely on real templates files
     */
    public function testToHtml()
    {
        $templateResults = 'poll/result.phtml';
        $templatePoll = 'poll/active.phtml';

        /**
         * Test active poll template used
         */
        $voted = false;
        $mock = $this->getModelMock('poll/poll', array('isVoted'));
        $mock->expects($this->any())
            ->method('isVoted')
            ->will($this->returnValue($voted));
        $block = new Mage_Poll_Block_ActivePoll();
        $block->setPollTemplate($templatePoll, 'poll');
        $block->setPollTemplate($templateResults, 'results');
        $block->toHtml();
        $template = $block->getTemplate();
        $this->assertEquals($templatePoll, $template, 'Wrong template used for active poll');

        /**
         * Test poll results template used
         */
        $voted = true;
        $mock = $this->getModelMock('poll/poll', array('isVoted'));
        $mock->expects($this->any())
            ->method('isVoted')
            ->will($this->returnValue($voted));
        $block = new Mage_Poll_Block_ActivePoll();
        $block->setPollTemplate($templatePoll, 'poll');
        $block->setPollTemplate($templateResults, 'results');
        $block->toHtml();
        $template = $block->getTemplate();
        $this->assertEquals($templateResults, $template, 'Wrong template used for poll results');
    }

}
