<?php
/**
 * High-level interface for mail templates data that hides format from the client code
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mail\Template;

interface ConfigInterface
{
    /**
     * Retrieve unique identifiers of all available email templates
     *
     * @return string[]
     */
    public function getAvailableTemplates();

    /**
     * Retrieve translated label of an email template
     *
     * @param string $templateId
     * @return string
     */
    public function getTemplateLabel($templateId);

    /**
     * Retrieve type of an email template
     *
     * @param string $templateId
     * @return string
     */
    public function getTemplateType($templateId);

    /**
     * Retrieve fully-qualified name of a module an email template belongs to
     *
     * @param string $templateId
     * @return string
     */
    public function getTemplateModule($templateId);

    /**
     * Retrieve full path to an email template file
     *
     * @param string $templateId
     * @return string
     */
    public function getTemplateFilename($templateId);
}
