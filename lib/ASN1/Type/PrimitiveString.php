<?php

namespace ASN1\Type;

use ASN1\Component\Identifier;
use ASN1\Component\Length;
use ASN1\Exception\DecodeException;

/**
 * Base class for primitive strings.
 */
abstract class PrimitiveString extends StringType
{
    use PrimitiveType;
    
    /**
     *
     * @see \ASN1\Element::_encodedContentDER()
     * @return string
     */
    protected function _encodedContentDER()
    {
        return $this->_string;
    }
    
    /**
     *
     * @see \ASN1\Element::_decodeFromDER()
     * @return self
     */
    protected static function _decodeFromDER(Identifier $identifier, $data,
        &$offset)
    {
        $idx = $offset;
        if (!$identifier->isPrimitive()) {
            throw new DecodeException("DER encoded string must be primitive.");
        }
        $length = Length::expectFromDER($data, $idx);
        $str = $length->length() ? substr($data, $idx, $length->length()) : "";
        // substr should never return false, since length is
        // checked by Length::expectFromDER.
        assert('is_string($str)', "substr");
        $offset = $idx + $length->length();
        try {
            return new static($str);
        } catch (\InvalidArgumentException $e) {
            throw new DecodeException($e->getMessage(), null, $e);
        }
    }
}
