<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Poll model
 *
 * @method \Magento\Poll\Model\Resource\Poll _getResource()
 * @method \Magento\Poll\Model\Resource\Poll getResource()
 * @method string getPollTitle()
 * @method \Magento\Poll\Model\Poll setPollTitle(string $value)
 * @method \Magento\Poll\Model\Poll setVotesCount(int $value)
 * @method int getStoreId()
 * @method \Magento\Poll\Model\Poll setStoreId(int $value)
 * @method string getDatePosted()
 * @method \Magento\Poll\Model\Poll setDatePosted(string $value)
 * @method string getDateClosed()
 * @method \Magento\Poll\Model\Poll setDateClosed(string $value)
 * @method int getActive()
 * @method \Magento\Poll\Model\Poll setActive(int $value)
 * @method int getClosed()
 * @method \Magento\Poll\Model\Poll setClosed(int $value)
 * @method int getAnswersDisplay()
 * @method \Magento\Poll\Model\Poll setAnswersDisplay(int $value)
 *
 * @category    Magento
 * @package     Magento_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Poll\Model;

class Poll extends \Magento\Core\Model\AbstractModel
{
    const XML_PATH_POLL_CHECK_BY_IP = 'web/polls/poll_check_by_ip';

    protected $_pollCookieDefaultName = 'poll';
    protected $_answersCollection   = array();
    protected $_storeCollection     = array();

    /**
     * Core http
     *
     * @var \Magento\Core\Helper\Http
     */
    protected $_coreHttp = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Http $coreHttp,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreHttp = $coreHttp;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento\Poll\Model\Resource\Poll');
    }

    /**
     * Retrieve Cookie Object
     *
     * @return \Magento\Core\Model\Cookie
     */
    public function getCookie()
    {
        return \Mage::app()->getCookie();
    }

    /**
     * Get Cookie Name
     *
     * @param int $pollId
     * @return string
     */
    public function getCookieName($pollId = null)
    {
        return $this->_pollCookieDefaultName . $this->getPollId($pollId);
    }

    /**
     * Retrieve defined or current Id
     *
     * @deprecated since 1.7.0.0
     * @param int $pollId
     * @return int
     */
    public function getPoolId($pollId = null)
    {
        return $this->getPollId($pollId);
    }

    /**
     * Retrieve defined or current Id
     *
     * @param int|null $pollId
     * @return int
     */
    public function getPollId($pollId = null)
    {
        if (is_null($pollId)) {
            $pollId = $this->getId();
        }
        return $pollId;
    }

    /**
     * Check if validation by IP option is enabled in config
     *
     * @return bool
     */
    public function isValidationByIp()
    {
        return (1 == $this->_coreStoreConfig->getConfig(self::XML_PATH_POLL_CHECK_BY_IP));
    }

    /**
     * Declare poll as voted
     *
     * @param   int $pollId
     * @return  \Magento\Poll\Model\Poll
     */
    public function setVoted($pollId=null)
    {
        $this->getCookie()->set($this->getCookieName($pollId), $this->getPollId($pollId));

        return $this;
    }

    /**
     * Check if poll is voted
     *
     * @param   int $pollId
     * @return  bool
     */
    public function isVoted($pollId = null)
    {
        $pollId = $this->getPollId($pollId);

        // check if it is in cookie
        $cookie = $this->getCookie()->get($this->getCookieName($pollId));
        if (false !== $cookie) {
            return true;
        }

        // check by ip
        if (count($this->_getResource()->getVotedPollIdsByIp($this->_coreHttp->getRemoteAddr(), $pollId))) {
            return true;
        }

        return false;
    }

    /**
     * Get random active pool identifier
     *
     * @return int
     */
    public function getRandomId()
    {
        return $this->_getResource()->getRandomId($this);
    }

    /**
     * Get all ids for not closed polls
     *
     * @return array
     */
    public function getAllIds()
    {
        return $this->_getResource()->getAllIds($this);
    }

    /**
     * Add vote to poll
     *
     * @param \Magento\Poll\Model\Poll\Vote $vote
     * @return unknown
     */
    public function addVote(\Magento\Poll\Model\Poll\Vote $vote)
    {
        if ($this->hasAnswer($vote->getPollAnswerId())) {
            $vote->setPollId($this->getId())
                ->save();
            $this->setVoted();
        }
        return $this;
    }

    /**
     * Check answer existing for poll
     *
     * @param   mixed $answer
     * @return  boll
     */
    public function hasAnswer($answer)
    {
        $answerId = false;
        if (is_numeric($answer)) {
            $answerId = $answer;
        } elseif ($answer instanceof \Magento\Poll\Model\Poll\Answer) {
            $answerId = $answer->getId();
        }

        if ($answerId) {
            return $this->_getResource()->checkAnswerId($this, $answerId);
        }
        return false;
    }

    public function resetVotesCount()
    {
        $this->_getResource()->resetVotesCount($this);
        return $this;
    }


    public function getVotedPollsIds()
    {
        $idsArray = array();

        foreach ($this->getCookie()->get() as $cookieName => $cookieValue) {
            $pattern = '#^' . preg_quote($this->_pollCookieDefaultName, '#') . '(\d+)$#';
            $match   = array();
            if (preg_match($pattern, $cookieName, $match)) {
                if ($match[1] != \Mage::getSingleton('Magento\Core\Model\Session')->getJustVotedPoll()) {
                    $idsArray[$match[1]] = $match[1];
                }
            }
        }

        // load from db for this ip
        foreach ($this->_getResource()->getVotedPollIdsByIp($this->_coreHttp->getRemoteAddr()) as $pollId) {
            $idsArray[$pollId] = $pollId;
        }

        return $idsArray;
    }

    public function addAnswer($object)
    {
        $this->_answersCollection[] = $object;
        return $this;
    }

    public function getAnswers()
    {
        return $this->_answersCollection;
    }

    public function addStoreId($storeId)
    {
        $ids = $this->getStoreIds();
        if (!in_array($storeId, $ids)) {
            $ids[] = $storeId;
        }
        $this->setStoreIds($ids);
        return $this;
    }

    public function getStoreIds()
    {
        $ids = $this->_getData('store_ids');
        if (is_null($ids)) {
            $this->loadStoreIds();
            $ids = $this->getData('store_ids');
        }
        return $ids;
    }

    public function loadStoreIds()
    {
        $this->_getResource()->loadStoreIds($this);
    }

    public function getVotesCount()
    {
        return $this->_getData('votes_count');
    }
}
