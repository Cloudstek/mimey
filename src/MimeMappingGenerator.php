<?php

namespace Mimey;

/**
 * Generates a mapping for use in the MimeTypes class.
 *
 * Reads text in the format of httpd's mime.types and generates a PHP array containing the mappings.
 */
class MimeMappingGenerator
{
	/**
	 * Read the given mime.types file and return a mapping compatible with the MimeTypes class.
	 *
	 * @param string $magicFile Path to the mime.types magic file
	 *
	 * @return array The mapping.
	 */
	public function generateMapping($magicFile)
	{
		if (empty($magicFile)) {
			return MimeMappingBuilder::blank()->getMapping();
		}

		// Current mapping
		$mapping = array();

		// Magic MIME file
		$magicFile = fopen($magicFile, 'r');

		while (($line = fgets($magicFile)) !== false) {
			// Parse line
			$matchFound = preg_match('/^([^\s\#\/]+(?:\/[^\s]+)?)\s+([^\#]+)/', trim($line), $matches);

			// Skip if we don't have a match
			if ($matchFound === false || count($matches) !== 3) {
				continue;
			}

			// Get matches
			list($line, $mime, $extensions) = $matches;

			// Split extensions by space
			$extensions = explode(' ', $extensions);

			foreach ($extensions as $extension) {
				$extension = trim($extension);

				if (!empty($mime) && !empty($extension)) {
					$mapping['mimes'][$extension][] = $mime;
					$mapping['extensions'][$mime][] = $extension;
				}
			}
		}

		if (!feof($magicFile)) {
			fclose($magicfile);
			throw new \Exception('Unexpected EOF.');
		}

		fclose($magicFile);
		return $mapping;
	}
}
