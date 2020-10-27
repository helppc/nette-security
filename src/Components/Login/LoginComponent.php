<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity\Components\Login;

use HelpPC\NetteUtils\UI\Form;
use HelpPC\NetteUtils\UI\IFormFactory;
use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

class LoginComponent extends Control
{
    private IFormFactory $formFactory;
    private User $user;


    /** @var callable|null */
    private $onSuccess = null;
    /** @var callable|null */
    private $onFailure = null;


    public function __construct(IFormFactory $formFactory, User $user)
    {
        $this->formFactory = $formFactory;
        $this->user = $user;
    }

    public function addOnSuccess(callable $callable): self
    {
        $this->onSuccess = $callable;
        return $this;
    }

    public function addOnFailure(callable $callable): self
    {
        $this->onFailure = $callable;
        return $this;
    }

    protected function createComponentForm(): Form
    {
        $form = $this->formFactory->create(true);
        $form->addValidationEmail('email', 'security.loginForm.email', true, true);
        $form->addPassword('password', 'security.loginForm.password');
        $form->addSubmit('submit', 'security.loginForm.login');

        $component = $this;
        $form->onSuccess[] = function (Form $form, ArrayHash $values) use ($component) {
            try {
                $component->user->login(
                    $values->email,
                    $values->password
                );
                if (is_callable($component->onSuccess)) {
                    call_user_func($component->onSuccess);
                }
            } catch (AuthenticationException$exception) {
                $component->flashMessage('Something is wrong.', 'danger');
                $form->addError('Something is wrong.');
                if (is_callable($component->onFailure)) {
                    call_user_func($component->onFailure);
                }
            }

        };

        return $form;
    }

    public function render(string $template = __DIR__ . '/login.latte'): void
    {
        $this->getTemplate()->setFile($template);
        $this->getTemplate()->render();
    }
}