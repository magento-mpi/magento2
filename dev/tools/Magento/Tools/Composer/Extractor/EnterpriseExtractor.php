<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

/**
 * Extractor for Enterprise Product
 */
class EnterpriseExtractor extends CommunityExtractor
{
    /**
     * Name of Package
     *
     * @var string
     */
    protected $_name = "Magento/Enterprise";

    /**
     * Version of Package
     *
     * @var string
     */
    protected $_version = "2.1.0";
}
