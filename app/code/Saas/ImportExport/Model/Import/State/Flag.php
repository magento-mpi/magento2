<?php
/**
 * Export Flag
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Import_State_Flag extends Saas_ImportExport_Model_StateFlag
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'import_entity';

    /**#@+
     * Flag max lifetime after last update
     */
    const FLAG_LIFETIME = 1800;
    /**#@-*/
}
