<?php namespace spec\Deefour\Interactor;

use Deefour\Interactor\Context;
use Deefour\Interactor\Exception\MarshalException;
use Deefour\Interactor\Stub\Contexts\MixedContext;
use Deefour\Interactor\Stub\Contexts\PlainContext;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContextFactorySpec extends ObjectBehavior {

  function it_should_construct_a_context_regardless_of_parameter_order() {
    $source  = [ 'bar' => 'baz', 'make' => 'foo', 'model' => 'bar' ];
    $context = $this->create(MixedContext::class, $source);

    $context->shouldBeAnInstanceOf(MixedContext::class);
    $context->make->shouldBe('foo');
    $context->bar->shouldBe('baz');
    $context->raw()->shouldBe([ 'bar' => 'baz' ]);
  }

  function it_should_create_a_mutable_transformer_based_context_by_default() {
    $context = $this->create([ 'foo' => true, 'bar' => false ]);

    $context->shouldBeAnInstanceOf(Context::class);
    $context->foo->shouldBe(true);
  }

  function it_should_passthru_a_previously_instantiated_context() {
    $original = new PlainContext([ 'foo' => true, 'bar' => false ]);

    $context = $this->create($original);

    $context->shouldBe($original);
    $context->foo->shouldBe(true);
  }

  function it_should_assign_properties_and_source_attributes_on_the_context() {
    $source  = [ 'make' => 'foo', 'model' => 'bar', 'attributes' => [ 'bar' => 'baz' ] ];
    $context = $this->create(MixedContext::class, $source);

    $context->shouldBeAnInstanceOf(MixedContext::class);
    $context->make->shouldBe('foo');
    $context->bar->shouldBe('baz');
    $context->raw('make')->shouldBeNull();
    $context->raw('bar')->shouldBe('baz');
  }

  function it_should_throw_marshal_exception_when_parameter_cannot_be_resolved() {
    $source = [ 'make' => 'foo', 'attributes' => [ 'bar' => 'baz' ] ];

    $this->shouldThrow(MarshalException::class)->during('create', [ MixedContext::class, $source ]);
  }

}
