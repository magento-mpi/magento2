<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Model_Flag extends Mage_Core_Model_Flag
{
    const STATE_QUEUED      = 1;
    const STATE_PROCESSING  = 2;
    const STATE_FINISHED    = 3;
    const STATE_NOTIFIED    = 4;

    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'refresh_search_index';
}
