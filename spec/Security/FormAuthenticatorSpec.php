<?php

namespace spec\Labstag\Security;

use Labstag\Security\FormAuthenticator;
use PhpSpec\ObjectBehavior;

class FormAuthenticatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FormAuthenticator::class);
    }
}
