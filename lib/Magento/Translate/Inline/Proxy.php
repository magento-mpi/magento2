<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Translate\Inline;

/**
 * Proxy class for \Magento\Translate\Inline
 */
class Proxy extends \Magento\Translate\Inline
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $instanceName;

    /**
     * Proxied instance
     *
     * @var \Magento\DesignEditor\Model\Translate\Inline
     */
    protected $subject;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $isShared;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $instanceName = 'Magento\Translate\Inline',
        $shared = true
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
        $this->isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array('_subject', '_isShared');
    }

    /**
     * Retrieve ObjectManager from global scope
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     *
     * @return void
     */
    public function __clone()
    {
        $this->subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Magento\DesignEditor\Model\Translate\Inline
     */
    protected function _getSubject()
    {
        if (!$this->subject) {
            $this->subject = true === $this->isShared
                ? $this->objectManager->get($this->instanceName)
                : $this->objectManager->create($this->instanceName);
        }
        return $this->subject;
    }

    /**
     * Check if Inline Translates is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_getSubject()->isAllowed();
    }

    /**
     * Retrieve Inline Parser instance
     *
     * @return \Magento\Translate\Inline\ParserInterface
     */
    public function getParser()
    {
        return $this->_getSubject()->getParser();
    }

    /**
     * Replace translation templates with HTML fragments
     *
     * @param array|string &$body
     * @param bool $isJson
     * @return $this
     */
    public function processResponseBody(&$body, $isJson = false)
    {
        return $this->_getSubject()->processResponseBody($body, $isJson);
    }

    /**
     * Additional translation mode html attribute is not needed for base inline translation.
     *
     * @param mixed|string|null $tagName
     * @return mixed
     */
    public function getAdditionalHtmlAttribute($tagName = null)
    {
        return $this->_getSubject()->getAdditionalHtmlAttribute($tagName);
    }
}
