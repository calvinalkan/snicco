<?php


    declare(strict_types = 1);


    namespace WPEmerge\Session\Events;

    use WP_User;
    use WPEmerge\Application\ApplicationEvent;

    class UserLoggedIn extends ApplicationEvent
    {

        /**
         * @var string
         */
        public $user_login;

        /**
         * @var WP_User
         */
        public $user;

        public function __construct(string $user_login, WP_User $user)
        {
            $this->user_login = $user_login;
            $this->user = $user;
        }

    }