<?php

namespace Deefour\Interactor;

use Deefour\Interactor\Exception\MarshalException;
use ReflectionClass;
use ReflectionParameter;

class ContextFactory
{
    /**
     * Resolves a context class.
     *
     * @param $attributes
     * @param $context
     *
     * @return object
     */
    public static function create($context, array $attributes = [])
    {
        if ($context instanceof Context) {
            return $context;
        }

        if (is_array($context)) {
            // no context was specified; just the attribute source was provided.
            $attributes = $context;
            $context    = Context::class;
        }

        $attributes  = array_merge(['attributes' => []], $attributes);
        $reflection  = new ReflectionClass($context);
        $constructor = $reflection->getConstructor();

        if ( ! $constructor) {
            return $reflection->newInstance();
        }

        // Fetch the parameter names from the constructor.
        $parameterNames = array_map(function ($parameter) {
            return $parameter->name;
        }, $constructor->getParameters());

        // Get list of keys from the attribute source that do NOT match the parameter names
        $extraParameters = array_diff_key($attributes, array_flip($parameterNames));

        // Get the key/value pairs from the attribute source for the extras
        $extraAttributes = array_intersect_key($attributes, $extraParameters);
        $attributes      = array_diff_key($attributes, $extraParameters);

        // Create/merge a special 'attributes' parameter with the non-matching arguments
        $attributes['attributes'] = array_merge(
            isset($attributes['attributes']) ? $attributes['attributes'] : [],
            $extraAttributes
          );

        if (empty($attributes['attributes'])) {
            unset($attributes['attributes']);
        }

        // Walk through the constructor signature again, mapping parameters to attributes from the source
        $parameters = array_map(function ($parameter) use ($context, $attributes) {
            return static::getParameterValueForContext($context, $parameter, $attributes);
        }, $constructor->getParameters());

        return $reflection->newInstanceArgs($parameters);
    }

    /**
     * Get a parameter value for a marshaled command.
     *
     * @param string               $context
     * @param \ReflectionParameter $parameter
     * @param array                $attributes
     *
     * @return mixed
     *
     * @throws MarshalException
     */
    protected static function getParameterValueForContext(
        $context,
        ReflectionParameter $parameter,
        array $attributes = []
    ) {
        if (array_key_exists($parameter->name, $attributes)) {
            return $attributes[ $parameter->name ];
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new MarshalException(sprintf(
            'Could not resolve [%s] parameter on [%s]',
            $parameter->name,
            $context
        ));
    }
}
