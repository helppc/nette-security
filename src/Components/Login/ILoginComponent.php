<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity\Components\Login;

interface ILoginComponent
{

    public function create(): LoginComponent;

}