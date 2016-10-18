<?php

namespace Deefour\Interactor;

use InvalidArgumentException;

/**
 * Organizes a sequence of contexts, indexing them by their individual FQCNs.
 */
class CompositeContext extends Context
{
    /**
     * Constructor.
     *
     * Accepting an array of contexts, sets the keys of the source attributes to
     * the FQCN of each context class and the value to the contexts themselves.
     *
     * @param array $contexts
     */
    public function __construct(array $contexts)
    {
        $this->verifyContexts($contexts);

        $contexts = array_combine(
            array_map(function ($c) {
                return get_class($c);
            }, $contexts),
            $contexts
        );

        parent::__construct($contexts);
    }

    /**
     * Shares $attribute from $contextSrc with $contextDest
     *
     * @param  string $contextSrc   The context FQCN to get $attribute from (source)
     * @param  string $contextDest  The context FQCN to share $attribute with (destination)
     * @param  string $attribute    The attribute name as a string
     *
     * @return void
     */
    public function share($contextSrc, $contextDest, $attribute)
    {
        $contextSrc  = $this->get($contextSrc);
        $contextDest = $this->get($contextDest);

        if (isset($contextSrc) && isset($contextDest) && isset($contextSrc->{$attribute})) {
            $contextDest->{$attribute}($contextSrc->{$attribute});
        }
    }

    /**
     * Type-check each context in the source array provided to the composite.
     * Throw an exception if an argument does not subclass the base context.
     *
     * @throws InvalidArgumentException
     * @param  array  $contexts
     * @return void
     */
    private function verifyContexts(array $contexts)
    {
        foreach ($contexts as $context) {
            if (!($context instanceof Context)) {
                throw new InvalidArgumentException(
                    sprintf(
                        '[%s] expects only arguments that subclass [%s]; ' .
                        'instance of [%s] provided.',
                        static::class,
                        Context::class,
                        get_class($context)
                    )
                );
            }
        }
    }
}
