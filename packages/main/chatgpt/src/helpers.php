<?php

if (!function_exists('getClientIp')) {
    /**
     * Get the client IP address.
     *
     * @return string
     */
    function getClientIp(): string
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return 'UNKNOWN';
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('env')) {
    /**
     * Get the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        $value = trim(getenv($key));

        if ($value === '') {
            return value($default);
        }

        switch (mb_strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (mb_strlen($value) > 1 && mb_substr($value, 0, 1) === '"' && mb_substr($value, -1) === '"') {
            return mb_substr($value, 1, -1);
        }

        if (is_numeric($value)) {
            return $value + 0;
        }

        if (preg_match('/^([a-zA-Z0-9_]+):(.*)$/', $value, $matches)) {
            switch ($matches[1]) {
                case 'base64':
                    return base64_decode($matches[2]);
                case 'json':
                    return json_decode($matches[2], true);
                case 'serialize':
                    return unserialize($matches[2]);
            }
        }

        return value($value);
    }
}
