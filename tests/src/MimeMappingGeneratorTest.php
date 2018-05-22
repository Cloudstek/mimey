<?php

use PHPUnit\Framework\TestCase;

class MimeMappingGeneratorTest extends TestCase
{
	public function testGenerateMapping()
	{
		$generator = new \Mimey\MimeMappingGenerator();
		$mapping = $generator->generateMapping(__DIR__ . '/../fixtures/mime.types');
		$expected = array(
			'mimes' => array(
				'json' => array('application/json'),
				'jpeg' => array('image/jpeg'),
				'jpg' => array('image/jpeg'),
				'uvi' => array('image/vnd.dece.graphic'),
				'uvvi' => array('image/vnd.dece.graphic'),
				'uvg' => array('image/vnd.dece.graphic'),
				'uvvg' => array('image/vnd.dece.graphic'),
				'bar' => array('foo', 'qux'),
				'baz' => array('foo'),
			),
			'extensions' => array(
				'application/json' => array('json'),
				'image/jpeg' => array('jpeg', 'jpg'),
				'image/vnd.dece.graphic' => array('uvi', 'uvvi', 'uvg', 'uvvg'),
				'foo' => array('bar', 'baz'),
				'qux' => array('bar')
			)
		);
		$this->assertEquals($expected, $mapping);
	}
}
