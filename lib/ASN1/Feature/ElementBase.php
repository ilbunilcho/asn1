<?php

namespace ASN1\Feature;

/**
 * Base interface for ASN.1 type elements.
 */
interface ElementBase extends Encodable
{
    /**
     * Get the class of the ASN.1 type.
     *
     * One of <code>Identifier::CLASS_*</code> constants.
     *
     * @return int
     */
    public function typeClass();
    
    /**
     * Check whether the element is constructed.
     *
     * Otherwise it's primitive.
     *
     * @return bool
     */
    public function isConstructed();
    
    /**
     * Get the tag of the element.
     *
     * Interpretation of the tag depends on the context. For example it may
     * represent a universal type tag or a tag of an implicitly or explicitly
     * tagged type.
     *
     * @return int
     */
    public function tag();
    
    /**
     * Check whether the element is a type of a given tag.
     *
     * @param int $tag Type tag
     * @return boolean
     */
    public function isType($tag);
    
    /**
     * Check whether the element is a type of a given tag.
     *
     * Throws an exception if expectation fails.
     *
     * @param int $tag Type tag
     * @throws \UnexpectedValueException If the element type differs from the
     *         expected
     * @return ElementBase
     */
    public function expectType($tag);
    
    /**
     * Check whether the element is tagged (context specific).
     *
     * @return bool
     */
    public function isTagged();
    
    /**
     * Check whether the element is tagged (context specific) and optionally has
     * a given tag.
     *
     * Throws an exception if the element is not tagged or tag differs from
     * the expected.
     *
     * @param int|null $tag Optional type tag
     * @throws \UnexpectedValueException If expectation fails
     * @return \ASN1\Type\TaggedType
     */
    public function expectTagged($tag = null);
    
    /**
     * Get the object as an abstract Element instance.
     *
     * @return \ASN1\Element
     */
    public function asElement();
}
