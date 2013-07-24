<?php
/**
 * Generic action controller for all services available via web API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Webapi_Controller_ActionAbstract
{
    /**#@+
     * Collection page sizes.
     */
    const PAGE_SIZE_DEFAULT = 10;
    const PAGE_SIZE_MAX = 100;
    /**#@-*/

    /**#@+
     * Allowed API service methods.
     */
    const METHOD_CREATE = 'create';
    const METHOD_GET = 'get';
    const METHOD_LIST = 'list';
    const METHOD_UPDATE = 'update';
    const METHOD_DELETE = 'delete';
    const METHOD_MULTI_UPDATE = 'multiUpdate';
    const METHOD_MULTI_DELETE = 'multiDelete';
    const METHOD_MULTI_CREATE = 'multiCreate';
    /**#@-*/

}
