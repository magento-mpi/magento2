<?php
/**
 * Mail Template interface
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Mail\Template;

interface TemplateInterface
{
    /**
     * Types of template
     */
    const TYPE_TEXT = 1;
    const TYPE_HTML = 2;

    /**
     * Get processed template
     *
     * @return string
     */
    public function processTemplate();

    /**
     * Get processed subject
     *
     * @return string
     */
    public function getSubject();

    /**
     * Get Type
     *
     * @return int
     */
    public function getType();

    /**
     * Set template variables
     *
     * @param array $vars
     * @return $this
     */
    public function setVars($vars);

    /**
     * Set template options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions($options);
}
