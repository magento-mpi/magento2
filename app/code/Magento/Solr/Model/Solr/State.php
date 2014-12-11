<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
