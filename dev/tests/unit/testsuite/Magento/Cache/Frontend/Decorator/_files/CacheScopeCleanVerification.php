<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class CacheScopeCleanVerification
{
    /**
     * @var array
     */
    protected $_records = array(
        'elephant' => array('mammal', 'big'),
        'man' => array('mammal', 'medium'),
        'raccoon' => array('mammal', 'small'),
        'ostrich' => array('bird', 'big'),
        'turkey' => array('bird', 'medium'),
        'pigeon' => array('bird', 'small')
    );

    /**
     * @var array
     */
    protected $_oldRecordIds = array('elephant', 'turkey');

    /**
     * Clean records according to tags and mode
     *
     * @param string $mode
     * @param array $tags
     * @return bool
     */
    public function clean($mode, $tags)
    {
        switch ($mode) {
            case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
                $filterFunc = function ($recTags) use ($tags) {
                    return (bool) array_diff($tags, $recTags);
                };
                break;
            case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                $filterFunc = function ($recTags) use ($tags) {
                    return !array_intersect($recTags, $tags);
                };
            case Zend_Cache::CLEANING_MODE_ALL:
                $filterFunc = function () {
                    return false;
                };
                break;
            case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
                $filterFunc = function ($recTags) use ($tags) {
                    return array_intersect($recTags, $tags);
                };
                break;
            case Zend_Cache::CLEANING_MODE_OLD:
                foreach ($this->_oldRecordIds as $oldRecordId) {
                    unset($this->_records[$oldRecordId]);
                }
                return true;
            default:
                return false;
        }
        $this->_records = array_filter($this->_records, $filterFunc);
        return true;
    }

    /**
     * Return id of records left
     *
     * @return array
     */
    public function getRecordIds()
    {
        return array_keys($this->_records);
    }
}
