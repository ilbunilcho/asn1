<?php

namespace ASN1\Type\Primitive;

use ASN1\Type\PrimitiveString;
use ASN1\Type\UniversalClass;

/**
 * Implements <i>VideotexString</i> type.
 */
class VideotexString extends PrimitiveString
{
    use UniversalClass;
    
    /**
     * Constructor.
     *
     * @param string $string
     */
    public function __construct($string)
    {
        $this->_typeTag = self::TYPE_VIDEOTEX_STRING;
        parent::__construct($string);
    }
    
    /**
     *
     * {@inheritdoc}
     */
    protected function _validateString($string)
    {
        // allow everything
        return true;
    }
}
