<?php

namespace Mimey;

/**
 * Class for loading and saving MIME data
 */
class MimeMappingLoader implements MimeMappingLoaderInterface
{
	/**
	 * Load MIME mapping data
	 *
	 * @param string $magicFile Path to mime.types magic file
	 * @param string|null $cacheFile Path to custom cache file location
	 *
	 * @return array Mapping data
	 */
	public function load($magicFile, $cacheFile = null)
	{
		if (!empty($cacheFile) && file_exists($cacheFile)) {
			try {
				$cacheFile = file_get_contents($cacheFile);
				$mapping = extension_loaded('igbinary') ? igbinary_unserialize($cacheFile) : unserialize($cacheFile);

				if (!is_array($mapping) || (!empty($magicFile) && empty($mapping['hash']))) {
					$mapping = null;
				}

				// Detect file changes
				if (empty($magicFile) || $mapping['hash'] === hash_file('md5', $magicFile)) {
					unset($mapping['hash']);
					return $mapping;
				}
			} catch (\Exception $ex) {
				$mapping = null;
			}
		}

		// Start with blank mapping
		$mapping = MimeMappingBuilder::blank()->getMapping();

		if (!empty($magicFile)) {
			$mapping = (new MimeMappingGenerator())->generateMapping($magicFile);

			// Calculate hash to detect file changes
			$mapping['hash'] = hash_file('md5', $magicFile);
		}

		if (empty($cacheFile)) {
			$cacheFile = $magicFile.'.db';
		}

		$this->save($mapping, $cacheFile);

		// Remove hash from returned results
		unset($mapping['hash']);

		return $mapping;
	}

	/**
	 * Save MIME mapping
	 *
	 * @param array $mapping MIME mapping
	 * @param string $file Cache file to save to
	 */
	public function save($mapping, $file)
	{
		if (!is_array($mapping)) {
			throw new \InvalidArgumentException('Mapping must be an array.');
		}

		$data = extension_loaded('igbinary') ? igbinary_serialize($mapping) : serialize($mapping);

		$result = file_put_contents($file, $data);

		if ($result === false) {
			throw new \Exception('Could not save mapping to file.');
		}
	}
}
