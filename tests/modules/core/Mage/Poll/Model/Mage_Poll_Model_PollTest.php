<?php

class Mage_Poll_Model_PollTest extends Mage_TestCase
{

    public function test_constructor()
    {
        $poll = new Mage_Poll_Model_Poll();
        $this->assertIsMageModel($poll, "Poll is not a model instance");
        $this->assertIsMageResourceModel($poll->getResource(), "Poll doesn't have a valid resource");
        $this->assertIsMageResourceCollection($poll->getResourceCollection(), "Poll doesn't have a valid resource collection");
    }

    public function test_getCookie()
    {
        $cookie = Mage::getSingleton('core/cookie');
        $poll = new Mage_Poll_Model_Poll();
        $this->assertSame($cookie, $poll->getCookie());
    }

    public function test_getCookieName()
    {
        $poll = new Mage_Poll_Model_Poll();
        $pollId = md5(time());
        $this->assertContains($poll->getPoolId($pollId), $poll->getCookieName($pollId));
    }

    public function test_getPoolId()
    {
        $poll = $this->getMock('Mage_Poll_Model_Poll', array('getId'));
        $pollId = time();
        $poll->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($pollId));
        $this->assertEquals($pollId, $poll->getPoolId(null));
        $this->assertEquals($pollId, $poll->getPoolId($pollId));
    }

    /**
     * @TODO Enable this test when applying config fixtures will be implemented
     */
    public function test_isValidationByIp()
    {
        $this->markTestIncomplete();
        // $poll = new Mage_Poll_Model_Poll();
        // @TODO apply config fixture on store config (Mage_Poll_Model_Poll::XML_PATH_POLL_CHECK_BY_IP, 1);
        // $this->assertTrue($poll->isValidationByIp());
        // @TODO apply config fixture on store config (Mage_Poll_Model_Poll::XML_PATH_POLL_CHECK_BY_IP, 0);
        // $this->assertFalse($poll->isValidationByIp());
    }

    public function test_setVoted()
    {
        $cookieName = md5(time());
        $pollId = time();

        $cookie = $this->getMock('Varien_Object', array('set'));
        $poll = $this->getMock('Mage_Poll_Model_Poll', array('getCookie', 'getCookieName', 'getPoolId'));
        $poll->expects($this->any())
            ->method('getCookie')
            ->will($this->returnValue($cookie));
        $poll->expects($this->any())
            ->method('getCookieName')
            ->will($this->returnValue($cookieName))
            ->with($pollId);
        $poll->expects($this->any())
            ->method('getPoolId')
            ->will($this->returnValue($pollId))
            ->with($pollId);

        $cookie->expects($this->any())
            ->method('set')
            ->with($cookieName, $pollId);

        $poll->setVoted($pollId);
    }

    public function test_isVoted_byCookie()
    {
        $pollId = time();
        $cookie = $this->getMock('Varien_Object', array('get'));
        $cookie->expects($this->any())
            ->method('get')
            ->will($this->returnValue(true));
        $poll = $this->getMock('Mage_Poll_Model_Poll', array('getCookie'));
        $poll->expects($this->any())
            ->method('getCookie')
            ->will($this->returnValue($cookie));
        $this->assertTrue($poll->isVoted($pollId), 'Failed to check by cookie');
    }

    /**
     * @dataProvider provider_votedPolls
     */
    public function test_isVoted_byIp($votedPolls)
    {
        $pollId = time();
        $cookie = $this->getMock('Varien_Object', array('get'));
        $cookie->expects($this->any())
            ->method('get')
            ->will($this->returnValue(false));
        $resource = $this->getMock('Varien_Object', array('getVotedPollIdsByIp'));
        $resource->expects($this->any())
            ->method('getVotedPollIdsByIp')
            ->will($this->returnValue($votedPolls));
        $poll = $this->getMock('Mage_Poll_Model_Poll', array('_getResource', 'getCookie'));
        $poll->expects($this->any())
            ->method('getCookie')
            ->will($this->returnValue($cookie));
        $poll->expects($this->any())
            ->method('_getResource')
            ->will($this->returnValue($resource));
        $this->assertEquals(count($votedPolls)>0, $poll->isVoted($pollId), 'Failed to check by IP');
    }

    public function provider_votedPolls()
    {
        return array(
            array(array()),
            array(array(1)),
            array(array(1,2,3)),
        );
    }

    public function test_getRandomId()
    {
        $randomId = time();
        $poll = $this->getMock('Mage_Poll_Model_Poll', array('_getResource'));
        $resource = $this->getMock('Varien_Object', array('getRandomId'));
        $poll->expects($this->any())
            ->method('_getResource')
            ->will($this->returnValue($resource));

        $resource->expects($this->once())
            ->method('getRandomId')
            ->with($poll)
            ->will($this->returnValue($randomId));

        $this->assertEquals($randomId, $poll->getRandomId());
    }

    // @TODO

    public function test_getVotesCounts()
    {
        $poll = new Mage_Poll_Model_Poll();
        $random = time();
        $poll->setData('votes_count', $random);
        $this->assertEquals($random, $poll->getVotesCount());
    }

}
