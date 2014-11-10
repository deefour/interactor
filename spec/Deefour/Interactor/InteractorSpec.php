<?php namespace spec\Deefour\Interactor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Illuminate\Container\Container;
use Illuminate\Http\Request;

class InteractorSpec extends ObjectBehavior {

  function let(InteractorContext $context) {
    $this->beAnInstanceOf('spec\Deefour\Interactor\FailingInteractor');
    $this->beConstructedWith($context);
  }

  function it_is_initializable() {
    $this->shouldHaveType('Deefour\Interactor\Interactor');
  }

  function it_provides_the_status() {
    $this->status()->shouldReturnAnInstanceOf('Deefour\Interactor\Status');
  }

  function it_provides_the_context($context) {
    $this->context()->shouldReturn($context);
  }

  function it_is_ok_by_default() {
    $this->ok()->shouldReturn(true);
  }

  function it_is_not_ok_when_failure_occurs() {
    $this->perform()->ok()->shouldReturn(false);
  }

  function it_resolves_context_from_interactor_name(Container $container, PassingContext $context, Request $request) {
    $container->make('spec\Deefour\Interactor\PassingContext')->willReturn($context);

    $container->make('request')->willReturn($request);

    $request->get('foo', null)->willReturn('FOO');
    $request->get('bar', null)->willReturn('BAR');

    $this->beAnInstanceOf('spec\Deefour\Interactor\PassingInteractor');

    $this->setContainer($container)
         ->resolveContext();

    $this->context()->shouldReturnAnInstanceOf('spec\Deefour\Interactor\PassingContext');
  }

  function it_allows_access_to_public_methods_via_properties() {
    $this->__get('ok')->shouldReturn(true);
  }

  function it_returns_null_for_unknown_properties() {
    $this->__get('asdf')->shouldReturn(null);
  }

}



class FailingInteractor extends \Deefour\Interactor\Interactor {

  public function perform() {
    $this->fail('FAILURE');

    return $this;
  }

}

class PassingInteractor extends \Deefour\Interactor\Interactor {

  use \Deefour\Interactor\Traits\ResolvesDependencies;

  public function perform() {
    return $this;
  }

}

class PassingContext extends \Deefour\Interactor\Context {

  public function __construct($foo, $bar) {
    parent::__construct(get_defined_vars());
  }

}

class InteractorContext extends \Deefour\Interactor\Context {}