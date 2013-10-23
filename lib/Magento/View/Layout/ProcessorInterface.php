<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout;

interface ProcessorInterface
{
    /**
     * Add XML update instruction
     *
     * @param string $update
     * @return ProcessorInterface
     */
    public function addUpdate($update);

    /**
     * Get all registered updates as array
     *
     * @return array
     */
    public function asArray();

    /**
     * Get all registered updates as string
     *
     * @return string
     */
    public function asString();

    /**
     * Add handle(s) to update
     *
     * @param array|string $handleName
     * @return ProcessorInterface
     */
    public function addHandle($handleName);

    /**
     * Remove handle from update
     *
     * @param string $handleName
     * @return ProcessorInterface
     */
    public function removeHandle($handleName);

    /**
     * Get handle names array
     *
     * @return array
     */
    public function getHandles();

    /**
     * Add the first existing (declared in layout updates) page handle along with all parents to the update.
     * Return whether any page handles have been added or not.
     *
     * @param array $handlesToTry
     * @return ProcessorInterface
     */
    public function addPageHandles(array $handlesToTry);

    /**
     * Retrieve all page and fragment types that exist in the system.
     *
     * Result format:
     * array(
     *     'handle_name_1' => array(
     *         'name'     => 'handle_name_1',
     *         'label'    => 'Handle Name 1',
     *         'type'     => self::TYPE_PAGE or self::TYPE_FRAGMENT,
     *     ),
     *     // ...
     * )
     *
     * @return array
     */
    public function getAllPageHandles();

    /**
     * Load layout updates by handles
     *
     * @param array|string $handles
     * @throws \Magento\Exception
     * @return ProcessorInterface
     */
    public function load($handles = array());

    /**
     * Get layout updates as \Magento\View\Layout\Element object
     *
     * @return \SimpleXMLElement
     */
    public function asSimplexml();

    /**
     * Retrieve already merged layout updates from files for specified area/theme/package/store
     *
     * @return \Magento\View\Layout\Element
     */
    public function getFileLayoutUpdatesXml();

    /**
     * Retrieve containers from the update handles that have been already loaded
     *
     * Result format:
     * array(
     *     'container_name' => 'Container Label',
     *     // ...
     * )
     *
     * @return array
     */
    public function getContainers();
}
