<?php


    declare(strict_types = 1);


    namespace Tests\fixtures\Controllers\Web;

    use WPEmerge\Http\Psr7\Request;

    class RoutingController
    {


        public function foo(Request $request)
        {

            return 'foo';

        }


    }