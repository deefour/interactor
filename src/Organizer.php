<?php

namespace Deefour\Interactor;

use Deefour\Interactor\Contract\Interactor as InteractorContract;
use Deefour\Interactor\Exception\Failure;

/**
 * A composite interactor. Sequentially calls the interactors the organizer is
 * composed of. If a failure occurs, rollback will be called on each interactor
 * in reverse order.
 */
abstract class Organizer extends Interactor implements InteractorContract
{
    /**
     * The iterators that make up this organizer.
     *
     * @var array
     */
    protected $interactors = [];

    /**
     * The list of interactors that completed successfully.
     *
     * @var array
     */
    protected $completed = [];

    /**
     * Constructor.
     *
     * @param  Context $context
     */
    public function __construct(CompositeContext $context) {
        parent::__construct($context);
    }

    /**
     * Push an interactor onto the stack.
     *
     * @param Interactor $interactor
     */
    public function addInteractor(Interactor $interactor)
    {
        $this->interactors[] = $interactor;
    }

    /**
     * {@inheritdoc}
     */
    public function call()
    {
        $this->organize();
        $this->completed = [];

        try {
            foreach ($this->interactors as $interactor) {
                $interactor->call();

                $this->completed[] = $interactor;
            }
        } catch (Failure $e) {
            $this->rollback();

            $this->fail($e->getMessage());

            throw $e; // re-throw
        }
    }

    /**
     * Rollback the changes to the interactors that executed successfully. The
     * failing interactor will NOT be rolled back.
     */
    public function rollback()
    {
        foreach (array_reverse($this->completed) as $interactor) {
            $interactor->rollback();
        }
    }

    /**
     * Fetch the context by it's FQCN, also used as it's key on the organizer's
     * context.
     *
     * @param string $context
     *
     * @return Context
     */
    protected function getContext($context)
    {
        return $this->context()->get($context);
    }

    /**
     * Collect a list of interactors to run sequentially.
     *
     * @return array Class names for the interactors to execute.
     */
    abstract protected function organize();
}
