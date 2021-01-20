<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 * @copyright Copyright (c) 2019 Matthew Weier O'Phinney
 * @license   https://github.com/phly/keep-a-changelog/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace PhlyTest\KeepAChangelog\Version;

use Phly\KeepAChangelog\Config;
use Phly\KeepAChangelog\Provider\ProviderInterface;
use Phly\KeepAChangelog\Provider\ProviderSpec;
use Phly\KeepAChangelog\Version\ReleaseEvent;
use Phly\KeepAChangelog\Version\VerifyProviderCanReleaseListener;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class VerifyProviderCanReleaseListenerTest extends TestCase
{
    use ProphecyTrait;

    public function testListenerNotifiesEventThatProviderIsIncompleteIfProviderIsNotComplete()
    {
        $providerSpec = $this->prophesize(ProviderSpec::class);
        $providerSpec->isComplete()->willReturn(false);

        $config = $this->prophesize(Config::class);
        $config->provider()->will([$providerSpec, 'reveal']);

        $event = $this->prophesize(ReleaseEvent::class);
        $event->config()->will([$config, 'reveal']);
        $event->providerIsIncomplete()->shouldBeCalled();

        $listener = new VerifyProviderCanReleaseListener();

        $this->assertNull($listener($event->reveal()));

        $providerSpec->createProvider()->shouldNotHaveBeenCalled();
        $event->discoveredProvider(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function testListenerNotifiesEventThatProviderIsIncompleteIfProviderCannotRelease()
    {
        $provider = $this->prophesize(ProviderInterface::class);
        $provider->canCreateRelease()->willReturn(false);

        $providerSpec = $this->prophesize(ProviderSpec::class);
        $providerSpec->isComplete()->willReturn(true);
        $providerSpec->createProvider()->will([$provider, 'reveal']);

        $config = $this->prophesize(Config::class);
        $config->provider()->will([$providerSpec, 'reveal']);

        $event = $this->prophesize(ReleaseEvent::class);
        $event->config()->will([$config, 'reveal']);
        $event->providerIsIncomplete()->shouldBeCalled();

        $listener = new VerifyProviderCanReleaseListener();

        $this->assertNull($listener($event->reveal()));

        $event->discoveredProvider(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function testListenerNotifiesEventWithProviderWhenValid()
    {
        $provider = $this->prophesize(ProviderInterface::class);
        $provider->canCreateRelease()->willReturn(true);

        $providerSpec = $this->prophesize(ProviderSpec::class);
        $providerSpec->isComplete()->willReturn(true);
        $providerSpec->createProvider()->will([$provider, 'reveal']);

        $config = $this->prophesize(Config::class);
        $config->provider()->will([$providerSpec, 'reveal']);

        $event = $this->prophesize(ReleaseEvent::class);
        $event->config()->will([$config, 'reveal']);
        $event->discoveredProvider(Argument::that([$provider, 'reveal']))->shouldBeCalled();

        $listener = new VerifyProviderCanReleaseListener();

        $this->assertNull($listener($event->reveal()));

        $event->providerIsIncomplete()->shouldNotHaveBeenCalled();
    }
}
