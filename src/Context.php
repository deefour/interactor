<?php

namespace Deefour\Interactor;

use Deefour\Interactor\Exception\Failure;
use Deefour\Interactor\Status\Error;
use Deefour\Interactor\Status\Success;
use Deefour\Transformer\MutableTransformer;
use Exception;
use ReflectionMethod;

/**
 * Context object. Extends the Fluent class from illuminate/support,
 * making the creation and use of individual DTO's a breeze.
 *
 * The expectation is that the context objects extending this abstract class
 * default behavior will be to provide a type-hinted constructor signature,
 * passing the arguments up into this abstract class' constructor as a single,
 * associative array.
 *
 * Example:
 *
 * <code>
 * public function __constuct(array $attributes, User $user) {
 *   parent::__construct(get_defined_vars());
 * }
 * </code>
 */
class Context extends MutableTransformer
{
    /**
     * Object representing the current state of the interactor (passing/failing).
     *
     * @var Contract\Status
     */
    protected $status;

    /**
     * Getter for the current status/state of the interactor.
     *
     * @return Contract\Status
     */
    public function status()
    {
        return $this->status ?: new Success($this);
    }

    /**
     * Quick access to check if the state of the interactor is still condisered
     * 'passing'.
     *
     * @return bool
     */
    public function ok()
    {
        return $this->status() instanceof Success;
    }

    /**
     * Marks the state of the interactor as failed, setting a sensible messaeg
     * to explain what went wrong.
     *
     * @throws Exception
     * @param  string|Exception|null $reason
     */
    public function fail($reason = null)
    {
        if ($reason instanceof Exception) {
            $this->status = new Error($this, $reason->getMessage());

            throw new $reason;
        }

        $this->status = new Error($this, $reason);

        throw new Failure($this, $reason);
    }

    /**
     * Magic property access for public methods on the context.
     *
     * @param  string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        if (method_exists($this, $attribute) && (new ReflectionMethod($this, $attribute))->isPublic()) {
            return $this->$attribute();
        }

        return $this->get($attribute);
    }

    /**
     * Convenience for the message on the underlying status object.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->status()->__toString();
    }
}
