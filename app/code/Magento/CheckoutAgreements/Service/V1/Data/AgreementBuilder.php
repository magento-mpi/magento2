<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CheckoutAgreements\Service\V1\Data;

use \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class AgreementBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * Set agreement ID
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(Agreement::ID, $value);
    }

    /**
     * Set agreement name
     *
     * @param string $value
     * @return $this
     */
    public function setName($value)
    {
        return $this->_set(Agreement::NAME, $value);
    }

    /**
     * Set agreement content
     *
     * @param string $value
     * @return $this
     */
    public function setContent($value)
    {
        return $this->_set(Agreement::CONTENT, $value);
    }

    /**
     * Set agreement content height (optional CSS property)
     *
     * @param string $value
     * @return $this
     */
    public function setContentHeight($value)
    {
        return $this->_set(Agreement::CONTENT_HEIGHT, $value);
    }

    /**
     * Set agreement checkbox text
     *
     * @param string $value
     * @return $this
     */
    public function setCheckboxText($value)
    {
        return $this->_set(Agreement::CHECKBOX_TEXT, $value);
    }

    /**
     * Set agreement status
     *
     * @param bool $value
     * @return $this
     */
    public function setActive($value)
    {
        return $this->_set(Agreement::ACTIVE, $value);
    }

    /**
     * Set agreement content type
     *
     * @param bool $value
     * @return $this
     */
    public function setHtml($value)
    {
        return $this->_set(Agreement::HTML, $value);
    }
}
