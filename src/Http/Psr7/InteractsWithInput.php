<?php


    declare(strict_types = 1);


    namespace WPEmerge\Http\Psr7;

    use stdClass;
    use WPEmerge\Support\Arr;
    use WPEmerge\Support\Str;

    trait InteractsWithInput
    {

        public function validate (array $rules, array $attributes = [], array $messages = [] ) {

            $v = $this->validator();
            $v->rules($rules)
              ->messages($messages)
              ->attributes($attributes);

            return $v->validate($this->all());

        }

        public function all() : array
        {

            return $this->inputSource();

        }

        public function input($key = null, $default = null)
        {

            return data_get($this->all(), $key, $default);
        }

        public function server(string $key, $default = null)
        {

            return Arr::get($this->getServerParams(), $key, $default);

        }

        public function query(string $key = null, $default = null)
        {

            $query = $this->combinedQueryParams();

            if ( ! $key) {
                return $query;
            }

            return Arr::get($query, $key, $default);

        }

        public function queryString() :string {

            $qs = $this->getUri()->getQuery();

            while (Str::endsWith($qs, ['&', '='] ) ) {

                $qs = mb_substr($qs, 0, -1);

            }

            return $qs;


        }

        public function post(string $name = null, $default = null)
        {

            if ( ! $name) {

                return $this->getParsedBody() ?? [];

            }

            return Arr::get($this->getParsedBody(), $name, $default);

        }

        public function boolean($key = null, $default = false)
        {

            return filter_var($this->input($key, $default), FILTER_VALIDATE_BOOLEAN);
        }

        /**
         *
         * This method does not support * WILDCARDS
         *
         */
        public function only($keys) : array
        {

            $results = [];

            $input = $this->all();

            $placeholder = new stdClass;

            foreach (is_array($keys) ? $keys : func_get_args() as $key) {

                $value = data_get($input, $key, $placeholder);

                if ($value !== $placeholder) {

                    Arr::set($results, $key, $value);

                }
            }

            return $results;


        }

        /**
         *
         * This method does not support * WILDCARDS
         *
         */
        public function except($keys) : array
        {

            $keys = is_array($keys) ? $keys : func_get_args();

            $results = $this->all();

            Arr::forget($results, $keys);

            return $results;
        }

        public function has($key) : bool
        {

            $keys = is_array($key) ? $key : func_get_args();

            $input = $this->all();

            foreach ($keys as $value) {
                if ( ! Arr::has($input, $value)) {
                    return false;
                }
            }

            return true;
        }

        public function hasAny($keys) : bool
        {

            $keys = is_array($keys) ? $keys : func_get_args();

            $input = $this->all();

            return Arr::hasAny($input, $keys);
        }

        /**
         * Will return falls if any of the provided keys is missing.
         */
        public function missing($key) : bool
        {

            $keys = is_array($key) ? $key : func_get_args();

            return ! $this->has($keys);
        }

        public function old($key = null , $default = null)
        {

            $old = $this->session()->getOldInput();

            return  $key ? Arr::get($old, $key, $default) :$old;

        }

        private function inputSource() : array
        {

            $input = in_array($this->realMethod(), ['GET', 'HEAD'])
                ? $this->combinedQueryParams()
                : $this->getParsedBody();

            return (array) $input;

        }

        private function combinedQueryParams() : array
        {

            $query_string = $this->getUri()->getQuery();

            parse_str($query_string, $query);

            return array_merge($query, $this->getQueryParams());

        }


    }