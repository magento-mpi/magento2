<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
if (!extension_loaded('zip')) {
    /**
     * ZipArchive fake class to let unit tests go even without zip extension
     * @see http://www.php.net/manual/en/class.ziparchive.php
     */
    class ZipArchive
    {
        /**
         * Open a ZIP file archive
         *
         * @param string $filename
         * @param int $flags
         * @SuppressWarnings(PHPMD.UnusedFormalParameter)
         */
        public function open($filename, $flags = null)
        {
        }

        /**
         * Close the active archive (opened or newly created)
         */
        public function close()
        {
        }

        /**
         * Extract the archive contents
         * @param string $destination
         * @param mixed $entries
         * @SuppressWarnings(PHPMD.UnusedFormalParameter)
         */
        public function extractTo($destination, $entries = null)
        {
        }
    }
}
