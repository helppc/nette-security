<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity\DI;

use Contributte\Translation\DI\TranslationProviderInterface;
use HelpPC\NetteSecurity\Authenticator;
use HelpPC\NetteSecurity\Components\Login\ILoginComponent;
use HelpPC\NetteSecurity\Encoder\EncoderInterface;
use HelpPC\NetteSecurity\Encoder\SodiumPasswordEncoder;
use HelpPC\NetteSecurity\Encoder\UserPasswordEncoder;
use HelpPC\NetteSecurity\Encoder\UserPasswordEncoderInterface;
use HelpPC\NetteSecurity\Exception\RuntimeException;
use HelpPC\NetteSecurity\UserStorageProxy;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Security\IAuthenticator;
use Nette\Security\IUserStorage;

class SecurityExtension extends CompilerExtension implements TranslationProviderInterface
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'password' => Expect::structure([
                'timeCost' => Expect::int(8),
                'memoryCost' => Expect::string(13),
            ]),
            'authenticator' => Expect::string(Authenticator::class)->dynamic(),
        ]);

    }

    public function getTranslationResources(): array
    {
        return [__DIR__ . '/../lang'];
    }


    public function loadConfiguration()
    {

        $builder = $this->getContainerBuilder();

        $this->getContainerBuilder()
            ->addDefinition($this->prefix('user_storage'))
            ->setType(IUserStorage::class)
            ->setFactory(UserStorageProxy::class);

        $builder->addFactoryDefinition($this->prefix('loginComponent'))
            ->setImplement(ILoginComponent::class);

        $builder->addDefinition($this->prefix('authenticator'))
            ->setType(IAuthenticator::class)
            ->setFactory($this->getConfig()->authenticator);


        $builder->addDefinition($this->prefix('user_password_encoder'))
            ->setType(UserPasswordEncoderInterface::class)
            ->setFactory(UserPasswordEncoder::class);

        $builder->addDefinition($this->prefix('sodium_password_encoder'))
            ->setType(EncoderInterface::class)
            ->setFactory(SodiumPasswordEncoder::class, [
                'opsLimit' => $this->config->password->timeCost,
                'memLimit' => $this->config->password->memoryCost << 10,
            ]);

    }

    public function beforeCompile()
    {
        parent::beforeCompile();

        $builder = $this->getContainerBuilder();
        $userStorageProxy = $builder->getDefinition($this->prefix('user_storage'));

        foreach ($builder->findByType(IUserStorage::class) as $name => $userStorage) {
            if ($name !== $this->prefix('user_storage') && true === $userStorage->getAutowired()) {
                break;
            }
        }

        if (!isset($userStorage)) {
            throw new RuntimeException(sprintf(
                'Autowired service of type %s not found.',
                IUserStorage::class
            ));
        }

        $userStorage->setAutowired(false);

        $userStorageProxy->setAutowired(true);
        $userStorageProxy->setArguments([
            'userStorage' => $userStorage,
        ]);

    }

}