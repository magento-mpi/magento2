<?php
/**
 * Mail Template interface
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Mail;

interface TemplateInterface extends \Magento\App\TemplateTypesInterface
{
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
     * Set template variables
     *
     * @param array $vars
     * @return $this
     */
    public function setVars(array $vars);

    /**
     * Set template options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options);
}
