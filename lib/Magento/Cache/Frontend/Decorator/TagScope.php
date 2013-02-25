<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache frontend decorator that limits the cleaning scope within a tag
 */
class Magento_Cache_Frontend_Decorator_TagScope extends Magento_Cache_Frontend_Decorator_TagMarker
{
    /**
     * Limit the cleaning scope within a tag
     *
     * {@inheritdoc}
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, array $tags = array())
    {
        $enforcedTag = $this->getTag();
        if ($mode == Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG) {
            $result = false;
            foreach ($tags as $tag) {
                if (parent::clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($tag, $enforcedTag))) {
                    $result = true;
                }
            }
        } else {
            if ($mode == Zend_Cache::CLEANING_MODE_ALL) {
                $mode = Zend_Cache::CLEANING_MODE_MATCHING_TAG;
            }
            $tags[] = $enforcedTag;
            $result = parent::clean($mode, $tags);
        }
        return $result;
    }
}
