<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer;

interface CreatorInterface
{

    public function __construct($components, \Zend_Log $logger);

    public function create();
}