<?php

namespace ASN1\Component;

use ASN1\Decodable;
use ASN1\Encodable;
use ASN1\Exception\DecodeException;


/**
 * Class to represent BER/DER length octets
 */
class Length implements Decodable, Encodable
{
	/**
	 * Length
	 *
	 * @var int
	 */
	private $_length;
	
	/**
	 * Whether length is indefinite
	 *
	 * @var boolean
	 */
	private $_indefinite;
	
	/**
	 * Constructor
	 *
	 * @param int|string $length
	 * @param boolean $indefinite
	 */
	public function __construct($length, $indefinite = false) {
		$this->_length = $length;
		$this->_indefinite = $indefinite;
	}
	
	/**
	 * {@inheritDoc}
	 *
	 * @see Decodable::fromDER
	 * @param string $data
	 * @param int $offset
	 * @throws DecodeException
	 * @return self
	 */
	public static function fromDER($data, &$offset = null) {
		assert('is_string($data)', "got " . gettype($data));
		$idx = $offset ? $offset : 0;
		$datalen = strlen($data);
		if ($idx >= $datalen) {
			throw new DecodeException("Invalid offset");
		}
		$indefinite = false;
		$byte = ord($data[$idx++]);
		// bits 7 to 1
		$length = (0x7f & $byte);
		// long form
		if (0x80 & $byte) {
			$count = $length;
			// first octet must not be 0xff (spec 8.1.3.5c)
			if ($count == 127) {
				throw new DecodeException("Invalid number of length octets");
			}
			$num = gmp_init(0, 10);
			if ($count) {
				while (--$count >= 0) {
					if ($idx >= $datalen) {
						throw new DecodeException(
							"Unexpected end of data while decoding".
								" long form length");
					}
					$byte = ord($data[$idx++]);
					$num <<= 8;
					$num |= $byte;
				}
			} else { // indefinite form
				$indefinite = true;
			}
			$length = gmp_strval($num);
		}
		if (isset($offset)) {
			$offset = $idx;
		}
		return new self($length, $indefinite);
	}
	
	/**
	 * Decode length from DER.
	 * Throws an exception if length doesn't match with expected or if data
	 * doesn't contain enough bytes.
	 *
	 * @param string $data
	 * @param int $offset
	 * @param int|null $expected Expected length, null to bypass checking
	 * @throws DecodeException
	 * @return self
	 */
	public static function expectFromDER($data, &$offset, $expected = null) {
		$idx = $offset;
		$length = self::fromDER($data, $idx);
		// DER encoding must have definite length (spec 10.1)
		if ($length->isIndefinite()) {
			throw new DecodeException(
				"DER encoding must have definite length");
		}
		// if certain length was expected
		if (isset($expected) && $expected != $length->_length) {
			throw new DecodeException(
				"Expected length $expected, got {$length->_length}");
		}
		// check that enough data is available
		if (strlen($data) < $idx + $length->_length) {
			throw new DecodeException(
				"Length {$length->_length} overflows data, " .
					(strlen($data) - $idx) . " bytes left");
		}
		$offset = $idx;
		return $length;
	}
	
	/**
	 * {@inheritDoc}
	 *
	 * @see Encodable::toDER()
	 * @throws \DomainException If length is too large to encode
	 * @return string
	 */
	public function toDER() {
		$bytes = array();
		if ($this->_indefinite) {
			$bytes[] = 0x80;
		} else {
			$num = gmp_init($this->_length, 10);
			// long form
			if ($num > 127) {
				$octets = array();
				for (; $num > 0; $num >>= 8) {
					$octets[] = gmp_intval(0xff & $num);
				}
				$count = count($octets);
				// first octet must not be 0xff
				if ($count >= 127) {
					throw new \DomainException("Too many length octets");
				}
				$bytes[] = 0x80 | $count;
				foreach (array_reverse($octets) as $octet) {
					$bytes[] = $octet;
				}
			} else { // short form
				$bytes[] = gmp_intval($num);
			}
		}
		return pack("C*", ...$bytes);
	}
	
	/**
	 * Get length
	 *
	 * @throws \LogicException If length is indefinite
	 * @return int|string
	 */
	public function length() {
		if ($this->_indefinite) {
			throw new \LogicException("Length is indefinite");
		}
		return $this->_length;
	}
	
	/**
	 * Whether length is indefinite
	 *
	 * @return boolean
	 */
	public function isIndefinite() {
		return $this->_indefinite;
	}
}