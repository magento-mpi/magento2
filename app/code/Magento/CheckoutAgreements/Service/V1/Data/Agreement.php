<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CheckoutAgreements\Service\V1\Data;

use \Magento\Framework\Service\Data\AbstractExtensibleObject;

/**
 * @codeCoverageIgnore
 */
class Agreement extends AbstractExtensibleObject
{
    const ID = 'id';
    const NAME = 'name';
    const CONTENT = 'content';
    const CONTENT_HEIGHT = 'content_height';
    const CHECKBOX_TEXT = 'checkbox_text';
    const ACTIVE = 'active';
    const HTML = 'html';

    /**
     * Retrieve agreement ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Retrieve agreement name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Retrieve agreement content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_get(self::CONTENT);
    }

    /**
     * Retrieve agreement content height (optional CSS property)
     *
     * @return string|null
     */
    public function getContentHeight()
    {
        return $this->_get(self::CONTENT_HEIGHT);
    }

    /**
     * Retrieve agreement checkbox text
     *
     * @return string
     */
    public function getCheckboxText()
    {
        return $this->_get(self::CHECKBOX_TEXT);
    }

    /**
     * Retrieve agreement status
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->_get(self::ACTIVE);
    }

    /**
     * Retrieve agreement content type
     *
     * @return bool
     */
    public function isHtml()
    {
        return $this->_get(self::HTML);
    }
}
