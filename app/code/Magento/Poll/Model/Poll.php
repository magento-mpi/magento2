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
 * @method Magento_Poll_Model_Resource_Poll _getResource()
 * @method Magento_Poll_Model_Resource_Poll getResource()
 * @method string getPollTitle()
 * @method Magento_Poll_Model_Poll setPollTitle(string $value)
 * @method Magento_Poll_Model_Poll setVotesCount(int $value)
 * @method int getStoreId()
 * @method Magento_Poll_Model_Poll setStoreId(int $value)
 * @method string getDatePosted()
 * @method Magento_Poll_Model_Poll setDatePosted(string $value)
 * @method string getDateClosed()
 * @method Magento_Poll_Model_Poll setDateClosed(string $value)
 * @method int getActive()
 * @method Magento_Poll_Model_Poll setActive(int $value)
 * @method int getClosed()
 * @method Magento_Poll_Model_Poll setClosed(int $value)
 * @method int getAnswersDisplay()
 * @method Magento_Poll_Model_Poll setAnswersDisplay(int $value)
 *
 * @category    Magento
 * @package     Magento_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Poll_Model_Poll extends Magento_Core_Model_Abstract
{
    const XML_PATH_POLL_CHECK_BY_IP = 'web/polls/poll_check_by_ip';

    protected $_pollCookieDefaultName = 'poll';
    protected $_answersCollection   = array();
    protected $_storeCollection     = array();

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento_Poll_Model_Resource_Poll');
    }

    /**
     * Retrieve Cookie Object
     *
     * @return Magento_Core_Model_Cookie
     */
    public function getCookie()
    {
        return Mage::app()->getCookie();
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
     * @return  Magento_Poll_Model_Poll
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
        if (count($this->_getResource()->getVotedPollIdsByIp(Mage::helper('Magento_Core_Helper_Http')->getRemoteAddr(), $pollId))) {
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
     * @return unknown
     */
    public function addVote(Magento_Poll_Model_Poll_Vote $vote)
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
        }
        elseif ($answer instanceof Magento_Poll_Model_Poll_Answer) {
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
                if ($match[1] != Mage::getSingleton('Magento_Core_Model_Session')->getJustVotedPoll()) {
                    $idsArray[$match[1]] = $match[1];
                }
            }
        }

        // load from db for this ip
        foreach ($this->_getResource()->getVotedPollIdsByIp(Mage::helper('Magento_Core_Helper_Http')->getRemoteAddr()) as $pollId) {
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
