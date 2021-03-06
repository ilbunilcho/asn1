<?php

namespace ASN1;

use ASN1\Component\Identifier;
use ASN1\Component\Length;

/**
 * Container for raw DER encoded data.
 *
 * May be inserted into structure without decoding first.
 */
class DERData extends Element
{
    /**
     * DER encoded data.
     *
     * @var string $_der
     */
    protected $_der;
    
    /**
     * Identifier of the underlying type.
     *
     * @var Identifier $_identifier
     */
    protected $_identifier;
    
    /**
     * Offset to the content in DER data.
     *
     * @var int $_contentOffset
     */
    protected $_contentOffset = 0;
    
    /**
     * Constructor.
     *
     * @param string $data DER encoded data
     * @throws \ASN1\Exception\DecodeException If data does not adhere to DER
     */
    public function __construct($data)
    {
        $this->_identifier = Identifier::fromDER($data, $this->_contentOffset);
        Length::expectFromDER($data, $this->_contentOffset);
        $this->_der = $data;
        $this->_typeTag = intval($this->_identifier->tag());
    }
    
    /**
     *
     * @see \ASN1\Element::typeClass()
     * @return int
     */
    public function typeClass()
    {
        return $this->_identifier->typeClass();
    }
    
    /**
     *
     * @see \ASN1\Element::isConstructed()
     * @return bool
     */
    public function isConstructed()
    {
        return $this->_identifier->isConstructed();
    }
    
    /**
     *
     * @see \ASN1\Element::_encodedContentDER()
     * @return string
     */
    protected function _encodedContentDER()
    {
        // if there's no content payload
        if (strlen($this->_der) == $this->_contentOffset) {
            return "";
        }
        return substr($this->_der, $this->_contentOffset);
    }
    
    /**
     *
     * @see \ASN1\Element::toDER()
     * @return string
     */
    public function toDER()
    {
        return $this->_der;
    }
}
