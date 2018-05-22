<?php

namespace Mimey;

/**
 * An interface for loading and saving the MIMe mapping cache
 */
interface MimeMappingLoaderInterface
{
    /**
     * Load MIME mapping data
     *
     * @param string|null $magicFile Path to mime.types magic file
     * @param string|null $cacheFile Path to custom cache file location
     *
     * @return array Mapping data
     */
    public function load($magicFile, $cacheFile = null);

    /**
     * Save MIME mapping
     *
     * @param array $mapping MIME mapping
     * @param string $file Cache file to save to
     */
    public function save($mapping, $file);
}
