<?php
namespace Laravel
{
    use Laravel\Database\Connection;
    use Laravel\Database\Expression;
    use Laravel\Routing\Route;
    use Laravel\Routing\Router;
    use Symfony\Component\HttpFoundation\LaravelRequest as RequestFoundation;
    use Symfony\Component\HttpFoundation\LaravelResponse as FoundationResponse;
    use Symfony\Component\HttpFoundation\ResponseHeaderBag;

    /**
     * laravel\memcached.php
     */
    class Memcached
    {
        protected static $connection;

        public static function connection()
        {
            if (is_null(static ::$connection)) {
                static ::$connection = static ::connect(Config::get('cache.memcached'));
            }
            return static ::$connection;
        }

        public static function __callStatic($method, $parameters)
        {
            return call_user_func_array(array(static ::connection(), $method), $parameters);
        }

        protected static function connect($servers)
        {
            $memcache = new \Memcached;
            foreach ($servers as $server) {
                $memcache->addServer($server['host'], $server['port'], $server['weight']);
            }
            if ($memcache->getVersion() === false) {
                throw new \Exception('Could not establish memcached connection.');
            }
            return $memcache;
        }
    }

    /**
     * laravel\redis.php
     */
    class Redis
    {
        protected static $databases = array();
        protected $host;
        protected $port;
        protected $database;
        protected $connection;

        public function __construct($host, $port, $database = 0)
        {
            $this->host = $host;
            $this->port = $port;
            $this->database = $database;
        }

        public static function db($name = 'default')
        {
            if (!isset(static ::$databases[$name])) {
                if (is_null($config = Config::get("database.redis.{$name}"))) {
                    throw new \Exception("Redis database [$name] is not defined.");
                }
                extract($config);
                static ::$databases[$name] = new static ($host, $port, $database);
            }
            return static ::$databases[$name];
        }

        public static function __callStatic($method, $parameters)
        {
            return static ::db()->run($method, $parameters);
        }

        public function run($method, $parameters)
        {
            fwrite($this->connect(), $this->command($method, (array)$parameters));
            $response = trim(fgets($this->connection, 512));
            return $this->parse($response);
        }

        public function __call($method, $parameters)
        {
            return $this->run($method, $parameters);
        }

        public function __destruct()
        {
            if ($this->connection) {
                fclose($this->connection);
            }
        }

        protected function parse($response)
        {
            switch (substr($response, 0, 1)) {
                case '-':
                    throw new \Exception('Redis error: ' . substr(trim($response), 4));
                case '+':
                case ':':
                    return $this->inline($response);
                case '$':
                    return $this->bulk($response);
                case '*':
                    return $this->multibulk($response);
                default:
                    throw new \Exception("Unknown Redis response: " . substr($response, 0, 1));
            }
        }

        protected function connect()
        {
            if (!is_null($this->connection)) return $this->connection;
            $this->connection = @fsockopen($this->host, $this->port, $error, $message);
            if ($this->connection === false) {
                throw new \Exception("Error making Redis connection: {$error} - {$message}");
            }
            $this->select($this->database);
            return $this->connection;
        }

        protected function command($method, $parameters)
        {
            $command = '*' . (count($parameters) + 1) . CRLF;
            $command .= '$' . strlen($method) . CRLF;
            $command .= strtoupper($method) . CRLF;
            foreach ($parameters as $parameter) {
                $command .= '$' . strlen($parameter) . CRLF . $parameter . CRLF;
            }
            return $command;
        }

        protected function inline($response)
        {
            return substr(trim($response), 1);
        }

        protected function bulk($head)
        {
            if ($head == '$-1') return;
            list($read, $response, $size) = array(0, '', substr($head, 1));
            if ($size > 0) {
                do {
                    $block = (($remaining = $size - $read) < 1024) ? $remaining : 1024;
                    $response .= fread($this->connection, $block);
                    $read += $block;
                } while ($read < $size);
            }
            fread($this->connection, 2);
            return $response;
        }

        protected function multibulk($head)
        {
            if (($count = substr($head, 1)) == '-1') return;
            $response = array();
            for ($i = 0; $i < $count; $i++) {
                $response[] = $this->parse(trim(fgets($this->connection, 512)));
            }
            return $response;
        }
    }
}
namespace Laravel\Cache\Drivers
{
    use Laravel\Config;
    use Laravel\Database as DB;
    use Laravel\Database\Connection;
    /**
     * laravel\cache\drivers\sectionable.php
     */
    abstract class Sectionable extends Driver
    {
        public $implicit = true;
        public $delimiter = '::';
        public function get_from_section($section, $key, $default = null)
        {
            return $this->get($this->section_item_key($section, $key), $default);
        }
        public function put_in_section($section, $key, $value, $minutes)
        {
            $this->put($this->section_item_key($section, $key), $value, $minutes);
        }
        public function forever_in_section($section, $key, $value)
        {
            return $this->forever($this->section_item_key($section, $key), $value);
        }
        public function remember_in_section($section, $key, $default, $minutes, $function = 'put')
        {
            $key = $this->section_item_key($section, $key);
            return $this->remember($key, $default, $minutes, $function);
        }
        public function sear_in_section($section, $key, $default)
        {
            return $this->sear($this->section_item_key($section, $key), $default);
        }
        public function forget_in_section($section, $key)
        {
            return $this->forget($this->section_item_key($section, $key));
        }
        abstract public function forget_section($section);
        protected function sectionable($key)
        {
            return $this->implicit and $this->sectioned($key);
        }
        protected function sectioned($key)
        {
            return str_contains($key, '::');
        }
        protected function parse($key)
        {
            return explode('::', $key, 2);
        }
    }
    /**
     * laravel\cache\drivers\apc.php
     */
    class APC extends Driver
    {
        protected $key;
        public function __construct($key)
        {
            $this->key = $key;
        }
        public function has($key)
        {
            return (!is_null($this->get($key)));
        }
        protected function retrieve($key)
        {
            if (($cache = apc_fetch($this->key . $key)) !== false) {
                return $cache;
            }
        }
        public function put($key, $value, $minutes)
        {
            apc_store($this->key . $key, $value, $minutes * 60);
        }
        public function forever($key, $value)
        {
            return $this->put($key, $value, 0);
        }
        public function forget($key)
        {
            apc_delete($this->key . $key);
        }
    }
    /**
     * laravel\cache\drivers\database.php
     */
    class Database extends Driver
    {
        protected $key;
        public function __construct($key)
        {
            $this->key = $key;
        }
        public function has($key)
        {
            return (!is_null($this->get($key)));
        }
        protected function retrieve($key)
        {
            $cache = $this->table()->where('key', '=', $this->key . $key)->first();
            if (!is_null($cache)) {
                if (time() >= $cache->expiration) return $this->forget($key);
                return unserialize($cache->value);
            }
        }
        public function put($key, $value, $minutes)
        {
            $key = $this->key . $key;
            $value = serialize($value);
            $expiration = $this->expiration($minutes);
            try {
                $this->table()->insert(compact('key', 'value', 'expiration'));
            }
            catch(\Exception $e) {
                $this->table()->where('key', '=', $key)->update(compact('value', 'expiration'));
            }
        }
        public function forever($key, $value)
        {
            return $this->put($key, $value, 2628000);
        }
        public function forget($key)
        {
            $this->table()->where('key', '=', $this->key . $key)->delete();
        }
        protected function table()
        {
            $connection = DB::connection(Config::get('cache.database.connection'));
            return $connection->table(Config::get('cache.database.table'));
        }
    }
    /**
     * laravel\cache\drivers\file.php
     */
    class File extends Driver
    {
        protected $path;
        public function __construct($path)
        {
            $this->path = $path;
        }
        public function has($key)
        {
            return (!is_null($this->get($key)));
        }
        protected function retrieve($key)
        {
            if (!file_exists($this->path . $key)) return null;
            if (time() >= substr($cache = file_get_contents($this->path . $key), 0, 10)) {
                return $this->forget($key);
            }
            return unserialize(substr($cache, 10));
        }
        public function put($key, $value, $minutes)
        {
            if ($minutes <= 0) return;
            $value = $this->expiration($minutes) . serialize($value);
            file_put_contents($this->path . $key, $value, LOCK_EX);
        }
        public function forever($key, $value)
        {
            return $this->put($key, $value, 2628000);
        }
        public function forget($key)
        {
            if (file_exists($this->path . $key)) @unlink($this->path . $key);
        }
    }
    /**
     * laravel\cache\drivers\memcached.php
     */
    class Memcached extends Sectionable
    {
        public $memcache;
        protected $key;
        public function __construct(\Memcached $memcache, $key)
        {
            $this->key = $key;
            $this->memcache = $memcache;
        }
        public function has($key)
        {
            return (!is_null($this->get($key)));
        }
        protected function retrieve($key)
        {
            if ($this->sectionable($key)) {
                list($section, $key) = $this->parse($key);
                return $this->get_from_section($section, $key);
            } elseif (($cache = $this->memcache->get($this->key . $key)) !== false) {
                return $cache;
            }
        }
        public function put($key, $value, $minutes)
        {
            if ($this->sectionable($key)) {
                list($section, $key) = $this->parse($key);
                return $this->put_in_section($section, $key, $value, $minutes);
            } else {
                $this->memcache->set($this->key . $key, $value, $minutes * 60);
            }
        }
        public function forever($key, $value)
        {
            if ($this->sectionable($key)) {
                list($section, $key) = $this->parse($key);
                return $this->forever_in_section($section, $key, $value);
            } else {
                return $this->put($key, $value, 0);
            }
        }
        public function forget($key)
        {
            if ($this->sectionable($key)) {
                list($section, $key) = $this->parse($key);
                if ($key == '*') {
                    $this->forget_section($section);
                } else {
                    $this->forget_in_section($section, $key);
                }
            } else {
                $this->memcache->delete($this->key . $key);
            }
        }
        public function forget_section($section)
        {
            return $this->memcache->increment($this->key . $this->section_key($section));
        }
        protected function section_id($section)
        {
            return $this->sear($this->section_key($section), function ()
            {
                return rand(1, 10000);
            });
        }
        protected function section_key($section)
        {
            return $section . '_section_key';
        }
        protected function section_item_key($section, $key)
        {
            return $section . '#' . $this->section_id($section) . '#' . $key;
        }
    }
    /**
     * laravel\cache\drivers\memory.php
     */
    class Memory extends Sectionable
    {
        public $storage = array();
        public function has($key)
        {
            return (!is_null($this->get($key)));
        }
        protected function retrieve($key)
        {
            if ($this->sectionable($key)) {
                list($section, $key) = $this->parse($key);
                return $this->get_from_section($section, $key);
            } else {
                return array_get($this->storage, $key);
            }
        }
        public function put($key, $value, $minutes)
        {
            if ($this->sectionable($key)) {
                list($section, $key) = $this->parse($key);
                return $this->put_in_section($section, $key, $value, $minutes);
            } else {
                array_set($this->storage, $key, $value);
            }
        }
        public function forever($key, $value)
        {
            if ($this->sectionable($key)) {
                list($section, $key) = $this->parse($key);
                return $this->forever_in_section($section, $key, $value);
            } else {
                $this->put($key, $value, 0);
            }
        }
        public function forget($key)
        {
            if ($this->sectionable($key)) {
                list($section, $key) = $this->parse($key);
                if ($key == '*') {
                    $this->forget_section($section);
                } else {
                    $this->forget_in_section($section, $key);
                }
            } else {
                array_forget($this->storage, $key);
            }
        }
        public function forget_section($section)
        {
            array_forget($this->storage, 'section#' . $section);
        }
        public function flush()
        {
            $this->storage = array();
        }
        protected function section_item_key($section, $key)
        {
            return "section#{$section}.{$key}";
        }
    }
    /**
     * laravel\cache\drivers\redis.php
     */
    class Redis extends Driver
    {
        protected $redis;
        public function __construct(\Laravel\Redis $redis)
        {
            $this->redis = $redis;
        }
        public function has($key)
        {
            return (!is_null($this->redis->get($key)));
        }
        protected function retrieve($key)
        {
            if (!is_null($cache = $this->redis->get($key))) {
                return unserialize($cache);
            }
        }
        public function put($key, $value, $minutes)
        {
            $this->forever($key, $value);
            $this->redis->expire($key, $minutes * 60);
        }
        public function forever($key, $value)
        {
            $this->redis->set($key, serialize($value));
        }
        public function forget($key)
        {
            $this->redis->del($key);
        }
    }
    /**
     * laravel\cache\drivers\wincache.php
     */
    class WinCache extends Driver
    {
        protected $key;
        public function __construct($key)
        {
            $this->key = $key;
        }
        public function has($key)
        {
            return (!is_null($this->get($key)));
        }
        protected function retrieve($key)
        {
            if (($cache = wincache_ucache_get($this->key . $key)) !== false) {
                return $cache;
            }
        }
        public function put($key, $value, $minutes)
        {
            wincache_ucache_add($this->key . $key, $value, $minutes * 60);
        }
        public function forever($key, $value)
        {
            return $this->put($key, $value, 0);
        }
        public function forget($key)
        {
            wincache_ucache_delete($this->key . $key);
        }
    }
}
namespace Laravel\Session\Drivers
{
    use Laravel\Database\Connection;
    use Laravel\Config;
    /**
     * laravel\session\drivers\apc.php
     */
    class APC extends Driver
    {
        private $apc;
        public function __construct(\Laravel\Cache\Drivers\APC $apc)
        {
            $this->apc = $apc;
        }
        public function load($id)
        {
            return $this->apc->get($id);
        }
        public function save($session, $config, $exists)
        {
            $this->apc->put($session['id'], $session, $config['lifetime']);
        }
        public function delete($id)
        {
            $this->apc->forget($id);
        }
    }
    /**
     * laravel\session\drivers\database.php
     */
    class Database extends Driver implements Sweeper
    {
        protected $connection;
        public function __construct(Connection $connection)
        {
            $this->connection = $connection;
        }
        public function load($id)
        {
            $session = $this->table()->find($id);
            if (!is_null($session)) {
                return array('id' => $session->id, 'last_activity' => $session->last_activity, 'data' => unserialize($session->data));
            }
        }
        public function save($session, $config, $exists)
        {
            if ($exists) {
                $this->table()->where('id', '=', $session['id'])->update(array('last_activity' => $session['last_activity'], 'data' => serialize($session['data']),));
            } else {
                $this->table()->insert(array('id' => $session['id'], 'last_activity' => $session['last_activity'], 'data' => serialize($session['data'])));
            }
        }
        public function delete($id)
        {
            $this->table()->delete($id);
        }
        public function sweep($expiration)
        {
            $this->table()->where('last_activity', '<', $expiration)->delete();
        }
        private function table()
        {
            return $this->connection->table(Config::get('session.table'));
        }
    }
    /**
     * laravel\session\drivers\memcached.php
     */
    class Memcached extends Driver
    {
        private $memcached;
        public function __construct(\Laravel\Cache\Drivers\Memcached $memcached)
        {
            $this->memcached = $memcached;
        }
        public function load($id)
        {
            return $this->memcached->get($id);
        }
        public function save($session, $config, $exists)
        {
            $this->memcached->put($session['id'], $session, $config['lifetime']);
        }
        public function delete($id)
        {
            $this->memcached->forget($id);
        }
    }
    /**
     * laravel\session\drivers\redis.php
     */
    class Redis extends Driver
    {
        protected $redis;
        public function __construct(\Laravel\Cache\Drivers\Redis $redis)
        {
            $this->redis = $redis;
        }
        public function load($id)
        {
            return $this->redis->get($id);
        }
        public function save($session, $config, $exists)
        {
            $this->redis->put($session['id'], $session, $config['lifetime']);
        }
        public function delete($id)
        {
            $this->redis->forget($id);
        }
    }
}