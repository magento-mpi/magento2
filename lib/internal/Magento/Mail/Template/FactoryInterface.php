<?php
/**
 * Mail Template Factory interface
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Mail\Template;

interface FactoryInterface
{
    /**
     * @param string $identifier
     * @return \Magento\Mail\Template\TemplateInterface
     */
    public function get($identifier);
}