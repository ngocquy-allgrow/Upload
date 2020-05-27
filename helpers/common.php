<?php

/*
|--------------------------------------------------------------------------
| Common global system helper functions
|--------------------------------------------------------------------------
|
| This file contains system helper functions that might be used in this
| application. Some helpers could be short-handed by easy-to-remember
| functions instead of calling long class names and methods.
|
 */
if (!function_exists('time_now')) {
    function time_now() {
        return (new \DateTime())->format('Y-m-d H:i:s');
    }
}

if (!function_exists('createSubId')) {
    function createSubId($length = 30) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('customerId'))
{
    function customerId($count)
    {
        $string = createSubId(3);
        $idCus  = $string. str_pad(++$count, 5, '0', STR_PAD_LEFT)
                .substr(strtotime(time_now()), -3, 3);

        return $idCus;
    }
}

if (!function_exists('userId'))
{
    function userId()
    {
        return JWTAuth::user()->id;
    }
}

if (!function_exists('sql_value')) {
    /**
     * This function is used to get full SQL from a Builder
     *
     * @author mai.tan 2018/07/18
     * @param  string $value
     * @param  PDO    $pdo
     * @return string
     */
    function sql_value($value, $pdo = null)
    {
        if (is_null($value)) {
            $output = 'null';
        } elseif (is_array($value)) {
            $output = array_flatten(array_map('sql_value', $value));
        } elseif (is_string($value) || is_numeric($value)) {
            if (!is_null($pdo) && method_exists($pdo, 'quote')) {
                $output = $pdo->quote((string) $value);
            } else {
                $output = "'" . preg_replace('~[\x00\x0A\x0D\x1A\x22\x27\x5C]~u', '\\\$0', (string) $value) . "'";
            }
        } else {
            $output = $value;
        }

        return $output;
    }
}

if (!function_exists('sql_wrap')) {
    /**
     * This function is used to wrap DB column names
     *
     * @author mai.tan 2018/12/07
     * @param  string $value
     * @return string
     */
    function sql_wrap($value)
    {
        return \DB::getQueryGrammar()->wrap($value);
    }
}

if (!function_exists('sql_format')) {
    
    /**
     * This function is used to beautify SQL query
     *
     * @author mai.tan 2019/02/05
     * @param  string $query
     * @param  bool   $minify
     * @return string
     */
    function sql_format($query, $minify = false)
    {
        if (!class_exists('SqlFormatter')) {
            return trim(preg_replace(['/^[\040\t]{2,}/mi', '/\s+$/m'], ['  ', ''], $query));
        }

        if ($minify) {
            return SqlFormatter::compress($query);
        }

        return SqlFormatter::format($query, false);
    }
}

if (!function_exists('sql_query')) {
    /**
     * This function is used to execute a raw query
     * Use this to prevent memory leaks in DB::statement()
     *
     * @see https://github.com/laravel/framework/issues/1641
     * @author mai.tan 2019/02/07
     * @param  string $query
     * @param  PDO    $pdo
     * @return string
     */
    function sql_query($query, $pdo = null)
    {
        $rows = 0;

        try {
            if (!is_null($pdo) && method_exists($pdo, 'prepare')) {
                $statement = $pdo->prepare($query);
            } else {
                $statement = DB::connection()->getPdo()->prepare($query);
            }

            $statement->execute();
            $rows = $statement->rowCount();
        } catch (Exception $e) {
            app('db_log')->error($e->getMessage(), [sql_format($query)]);
        }

        return $rows;
    }
}

if (!function_exists('full_sql')) {
    /**
     * Generate full query from query builder
     *
     * @author mai.tan 2018/07/18
     * @param  Builder $builder
     * @return string
     */
    function full_sql($builder)
    {
        if (!$builder) {
            return '';
        }

        if (is_a($builder, Illuminate\Database\Eloquent\Builder::class)) {
            $builder = $builder->getQuery();
        }

        $sql      = $builder->toSql();
        $var      = $builder->getBindings();
        $bindings = $builder->getConnection()->prepareBindings($var);

        foreach ($bindings as $key => $binding) {
            $regex = is_numeric($key)
            ? "/\\?(?=(?:[^'\\\\']*'[^'\\\\']*')*[^'\\\\']*$)/u"
            : "/:{$key}(?=(?:[^'\\\\']*'[^'\\\\']*')*[^'\\\\']*$)/u";
            $sql = preg_replace($regex, sql_value($binding), $sql, 1);
        }

        return $sql;
    }
}

if (!function_exists('generatePassword')) {
    function generatePassword($length = 9, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '0123456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';
        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if(!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;

        return $dash_str;
    }
}


if (!function_exists('mailer')) {
    function mailer($view, array $data, $callback, $queue = null) {
        if (config('mail.pretend')) {
            return;
        }
        // send / queue the email
        if (config('mail.should_queue')) {
            $queue = null == $queue ? config('queue.priority.low') : config('queue.priority.high');
            return Mail::queue($view, $data, $callback, $queue);
        } else {
            return Mail::send($view, $data, $callback);
        }
    }
}