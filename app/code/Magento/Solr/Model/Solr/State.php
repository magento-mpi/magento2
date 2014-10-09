<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Solr;

class State
{
    /**
     * Return if solr extension is loaded
     *
     * @return bool
     */
    public function isActive()
    {
        return extension_loaded('solr');
    }
}
