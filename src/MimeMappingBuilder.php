<?php

namespace Mimey;

/**
 * Class for converting MIME types to file extensions and vice versa.
 */
class MimeMappingBuilder
{
    /**
     * Mapping
     * @var array
     */
    protected $mapping;

    /**
     * MIME mapping loader
     * @var MimeMappingLoaderInterface
     */
    protected $loader;

    /**
     * Create a new mapping builder.
     *
     * @param array $mapping An associative array containing two entries.
     * Entry "mimes" being an associative array of extension to array of MIME types.
     * Entry "extensions" being an associative array of MIME type to array of extensions.
     * Example:
     * <code>
     * array(
     *   'extensions' => array(
     *     'application/json' => array('json'),
     *     'image/jpeg'       => array('jpg', 'jpeg'),
     *     ...
     *   ),
     *   'mimes' => array(
     *     'json' => array('application/json'),
     *     'jpeg' => array('image/jpeg'),
     *     ...
     *   )
     * )
     * </code>
     * @param MimeMappingLoaderInterface $loader MIME mapping loader
     */
    private function __construct($mapping = null, $loader = null)
    {
        if ($mapping === null) {
            $mapping = array(
                'mimes' => array(),
                'extensions' => array()
            );
        }

        if ($loader === null) {
            $loader = new MimeMappingLoader();
        }

        $this->mapping = $mapping;
        $this->loader = $loader;
    }

    /**
     * Add a conversion.
     *
     * @param string $mime The MIME type.
     * @param string $extension The extension.
     * @param bool   $prepend_extension Whether this should be the preferred conversion for MIME type to extension.
     * @param bool   $prepend_mime Whether this should be the preferred conversion for extension to MIME type.
     */
    public function add($mime, $extension, $prepend_extension = true, $prepend_mime = true)
    {
        $existing_extensions = empty($this->mapping['extensions'][$mime]) ? array() : $this->mapping['extensions'][$mime];
        $existing_mimes = empty($this->mapping['mimes'][$extension]) ? array() : $this->mapping['mimes'][$extension];

        ($prepend_extension === true) ? array_unshift($existing_extensions, $extension) : array_push($existing_extensions, $extension);
        ($prepend_mime === true) ? array_unshift($existing_mimes, $mime) : array_push($existing_mimes, $mime);

        $this->mapping['extensions'][$mime] = array_unique($existing_extensions);
        $this->mapping['mimes'][$extension] = array_unique($existing_mimes);
    }

    /**
     * Get current mapping
     *
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * Save the current mapping to a file.
     *
     * @param string $file The file to save to.
     */
    public function save($file)
    {
        $this->loader->save($this->getMapping(), $file);
    }

    /**
     * Create a new mapping builder based on the built-in types.
     *
     * @return MimeMappingBuilder A mapping builder with built-in types loaded.
     */
    public static function create()
    {
        return self::load(__DIR__.'/../mime.types');
    }

    /**
     * Create a new mapping builder based on types from a file.
     *
     * @param string $file The magic mime.types file to load
     * @param MimeMappingLoaderInterface $loader MIME mapping loader
     *
     * @return MimeMappingBuilder A mapping builder with types loaded from a file.
     */
    public static function load($file, $loader = null)
    {
        if ($loader === null) {
            $loader = new MimeMappingLoader();
        }

        return new self($loader->load($file));
    }

    /**
     * Create a new mapping builder based on types from a cached mapping file.
     *
     * @param string $file The cached MIME mapping file to load
     * @param MimeMappingLoaderInterface $loader MIME mapping loader
     *
     * @return MimeMappingBuilder A mapping builder with types loaded from a file.
     */
    public static function loadCache($file, $loader = null)
    {
        if ($loader === null) {
            $loader = new MimeMappingLoader();
        }

        return new self($loader->load(null, $file));
    }

    /**
     * Create a new mapping builder that has no types defined.
     *
     * @return MimeMappingBuilder A mapping builder with no types defined.
     */
    public static function blank()
    {
        return new self(array('mimes' => array(), 'extensions' => array()));
    }
}
