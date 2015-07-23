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
     * {@inheritdoc}
     */
    protected function fail($message = null)
    {
        $this->context()->fail($message);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function call();

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        //
    }
}
