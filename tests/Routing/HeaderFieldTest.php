<?php

namespace Ergo\Tests\Routing;

use Ergo\Http;

/**
 * @author Paul Annesley <paul@annesley.cc>
 * @licence http://www.opensource.org/licenses/mit-license.php
 * @see http://github.com/pda/phool
 */
class HeaderFieldTest extends \UnitTestCase
{
	private $_normalizer;

	function setUp()
	{
		$this->_normalizer = new Http\HeaderCaseNormalizer();
	}

	// ----------------------------------------
	// header

	public function testHeaderField()
	{
		$header = new Http\HeaderField('Host', 'example.org');
		$this->assertEqual($header->getName(), 'Host');
		$this->assertEqual($header->getValue(), 'example.org');
		$this->assertEqual("$header", "Host: example.org\r\n");
	}

	public function testHeaderFieldNameNormalization()
	{
		$header = new Http\HeaderField('Example-header', 'x');
		$this->assertEqual($header->getName(), 'Example-Header');
		$this->assertEqual("$header", "Example-Header: x\r\n");
	}

	public function testHeaderFieldFromString()
	{
		$header = Http\HeaderField::fromString('test: blarg: meh');
		$this->assertEqual($header->getName(), 'Test');
		$this->assertEqual($header->getValue(), 'blarg: meh');
	}

	public function testRoundTrip()
	{
		$string = "Test: blarg: meh\r\n";

		$this->assertEqual(
			Http\HeaderField::fromString($string)->__toString(),
			$string
		);
	}

	// ----------------------------------------
	// case normalizer

	public function testNormalizeOneWord()
	{
		$this->assertEqual(
			$this->_normalizer->normalize('test'),
			'Test'
		);
	}

	public function testNormalizeTwoWords()
	{
		$this->assertEqual(
			$this->_normalizer->normalize('test-header'),
			'Test-Header'
		);
	}

	public function testNormalizeManyWords()
	{
		$this->assertEqual(
			$this->_normalizer->normalize('one-Two-three-Four-five'),
			'One-Two-Three-Four-Five'
		);
	}
}
