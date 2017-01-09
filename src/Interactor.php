<?php

namespace Deefour\Interactor;

abstract class Interactor
{
    /**
     * Context object containing data required to call the interactor behavior.
     *
     * @var Context
     */
    protected $context = null;

    /**
     * Configure the interactor, binding a context object and defaulting the state
     * of the interactor to passing/OK.
     *
     * @param Context $context [optional]
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function context()
    {
        return $this->context;
    }

    /**
     * Convenience method to fail the interactor, passing through to the Context.
     *
     * @param  string  $message [optional]
     * @throws Failure
     */
    protected function fail($message = null)
    {
        $this->context()->fail($message);
    }

    /**
     * {@inheritdoc}
     */
    public function call()
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        //
    }
}
