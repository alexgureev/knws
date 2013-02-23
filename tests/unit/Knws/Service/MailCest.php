<?php
namespace Knws\Service;
use Codeception\Util\Stub;

class MailCest
{
    protected $class = 'Knws\Service\Mail';

    // tests
    public function newTransport(\CodeGuy $I) {

        $I->wantTo('Test transport creating');
        $I->haveStub($stub = Stub::makeEmptyExcept('Knws\Service\Mail', 'newTransport'));
        $I->executeMethod($stub,'newTransport');

        $I->expect('$transport has \Swift_SmtpTransport type')
            ->seePropertyIs($stub, 'transport', '\Swift_SmtpTransport');

        /*
        $I->wantTo('create new user by name');
        $I->haveStub($user = Stub::makeEmptyExcept('Knws\MysqliDriver', 'create'));

        $user->setName('davert');
        $I->executeTestedMethodOn($user);

        $I->expect('user is validated and saved')
            ->seeMethodInvoked($user, 'validate')
            ->seeMethodInvoked($user, 'save');

        $I->haveStub($invalid_user = Stub::makeEmptyExcept('Knws\MysqliDriver', 'create', array(
            'validate' => function () { return false; }
        )));

        $I->expect('exception is thrown for invalid user')
            ->executeTestedMethodOn($invalid_user)
            ->seeExceptionThrown('Exception','User is invalid');

        $I->expect('exception is thrown while trying to create not new user')
            ->changeProperty($user,'isNew', false)
            ->executeTestedMethodOn($user)
            ->seeExceptionThrown('Swift_Events_TransportExceptionEvent', "User already created");

         */
    }

}