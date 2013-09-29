<?php
/**
 * Email templates configuration data container. Provides email templates configuration data.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Email\Template\Config;

class Data extends \Magento\Config\Data
{
    /**
     * @param \Magento\Core\Model\Email\Template\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     */
    public function __construct(
        \Magento\Core\Model\Email\Template\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache
    ) {
        parent::__construct($reader, $cache, 'email_templates');
    }
}
