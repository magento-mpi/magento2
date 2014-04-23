<?php
/**
 * Mail Template Factory interface
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Mail\Template;

interface FactoryInterface
{
    /**
     * @param string $identifier
     * @return \Magento\Framework\Mail\TemplateInterface
     */
    public function get($identifier);
}
