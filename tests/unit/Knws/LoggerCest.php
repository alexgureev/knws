<?php
namespace Knws;
use Codeception\Util\Stub as Stub;

class LoggerCest
{
    public $class = 'Knws\Logger';

    public function initTest(\CodeGuy $I)
    {

        /*
        $I->wantTo('Test transport creating');
        $I->haveStub($stub = Stub::makeEmptyExcept('Knws\Service\Mail', 'newTransport'));
        $I->executeMethod($stub,'newTransport');

        $I->expect('$transport has \Swift_SmtpTransport type')
            ->seePropertyIs($stub, 'transport', '\Swift_SmtpTransport');
        */
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

    public function emergency(\CodeGuy $I)
    {
        $I->wantTo('Test '.__CLASS__.'::'.__FUNCTION__);

        $fakeLogger = Stub::makeEmpty('Monolog\Logger');
        $I->haveStub($stub = Stub::makeEmptyExcept('Knws\Logger', __FUNCTION__));
        $I->changeProperty($stub, 'logger', $fakeLogger);
        $I->executeMethod($stub, __FUNCTION__);

        //$user = Stub::makeEmpty('User', array('getName' => function () { return 'davert'; }));
        //$user->save(); // is empty and returns NULL
        //$user->getName(); // return 'davert'
        //
        //$I->executeMethod($stub, 'emergency', 'WTF');
    }
}