<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\Cache;

use Magento\Framework\App;
use Magento\Framework\AppInterface;
use Magento\Framework\App\Console\Response;

/**
 * An application for managing cache status
 */
class ManagerApp implements AppInterface
{
    /**#@+
     * Request keys for managing caches
     */
    const KEY_TYPES = 'types';
    const KEY_SET = 'set';
    const KEY_CLEAN = 'clean';
    const KEY_FLUSH = 'flush';
    /**#@- */

    /**
     * Console response
     *
     * @var Response
     */
    private $response;

    /**
     * Requested changes
     *
     * @var array
     */
    private $requestArgs;
    /**
     * @var Manager
     */
    private $cacheManager;

    /**
     * Constructor
     *
     * @param Manager $cacheManager
     * @param Response $response
     * @param array $requestArgs
     */
    public function __construct(
        Manager $cacheManager,
        Response $response,
        array $requestArgs
    ) {
        $this->cacheManager = $cacheManager;
        $this->response = $response;
        $this->requestArgs = $requestArgs;
    }

    /**
     * {@inheritdoc}
     * @return Response
     */
    public function launch()
    {
        $types = $this->getRequestedTypes();

        $enabledTypes = [];
        if (isset($this->requestArgs[self::KEY_SET])) {
            $isEnabled = (bool)(int)$this->requestArgs[self::KEY_SET];
            $enabledTypes = $this->cacheManager->setEnabled($types, $isEnabled);
        }
        if (isset($this->requestArgs[self::KEY_FLUSH])) {
            $this->cacheManager->flush($types);
        } else {
            // If flush is requested, both enabled and requested cache types have already been cleaned by flush
            if (isset($this->requestArgs[self::KEY_CLEAN])) {
                $this->cacheManager->clean($types);
            } elseif (!empty($enabledTypes)) {
                $this->cacheManager->clean($enabledTypes);
            }
        }
        $output = "\nCurrent status:\n";
        foreach ($this->cacheManager->getStatus() as $cache => $status) {
            $output .= "$cache => " . ($status ? 'enabled' : 'disabled') . "\n";
        }
        $this->response->setBody($output);
        return $this->response;
    }

    /**
     * Maps requested type from request into the current registry of types
     *
     * @return string[]
     */
    private function getRequestedTypes()
    {
        $requestedTypes = [];
        if (isset($this->requestArgs[self::KEY_TYPES])) {
            $requestedTypes = explode(',', $this->requestArgs[self::KEY_TYPES]);
            $requestedTypes = array_filter(array_map('trim', $requestedTypes), 'strlen');
        }
        $availableTypes = $this->cacheManager->getAvailableTypes();
        if (empty($requestedTypes)) {
            return $availableTypes;
        } else {
            $unsupportedTypes = array_diff($requestedTypes, $availableTypes);
            if ($unsupportedTypes) {
                throw new \InvalidArgumentException(
                    "Following requested cache types are not supported: '" . join("', '", $unsupportedTypes) . "'.\n"
                    . "Supported types: " . join(", ", $availableTypes) . ""
                );
            }
            return array_values(array_intersect($availableTypes, $requestedTypes));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function catchException(App\Bootstrap $bootstrap, \Exception $exception)
    {
        $this->response->setBody($exception->getMessage());
        $this->response->terminateOnSend(false);
        $this->response->sendResponse();
        return false;
    }
}
