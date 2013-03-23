<?php
namespace Symfony\Component\HttpFoundation\Session\Attribute {
    use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface.php
     */
    interface AttributeBagInterface extends SessionBagInterface {
        public function has($name);
        public function get($name, $default = null);
        public function set($name, $value);
        public function all();
        public function replace(array $attributes);
        public function remove($name);
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag.php
     */
    class AttributeBag implements AttributeBagInterface, \IteratorAggregate, \Countable
    {
        private $name = 'attributes';
        private $storageKey;
        protected $attributes = array();
        public function __construct($storageKey = '_sf2_attributes')
        {
            $this->storageKey = $storageKey;
        }
        public function getName()
        {
            return $this->name;
        }
        public function setName($name)
        {
            $this->name = $name;
        }
        public function initialize(array & $attributes)
        {
            $this->attributes = & $attributes;
        }
        public function getStorageKey()
        {
            return $this->storageKey;
        }
        public function has($name)
        {
            return array_key_exists($name, $this->attributes);
        }
        public function get($name, $default = null)
        {
            return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
        }
        public function set($name, $value)
        {
            $this->attributes[$name] = $value;
        }
        public function all()
        {
            return $this->attributes;
        }
        public function replace(array $attributes)
        {
            $this->attributes = array();
            foreach ($attributes as $key => $value) {
                $this->set($key, $value);
            }
        }
        public function remove($name)
        {
            $retval = null;
            if (array_key_exists($name, $this->attributes)) {
                $retval = $this->attributes[$name];
                unset($this->attributes[$name]);
            }
            return $retval;
        }
        public function clear()
        {
            $return = $this->attributes;
            $this->attributes = array();
            return $return;
        }
        public function getIterator()
        {
            return new \ArrayIterator($this->attributes);
        }
        public function count()
        {
            return count($this->attributes);
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag.php
     */
    class NamespacedAttributeBag extends AttributeBag
    {
        private $namespaceCharacter;
        public function __construct($storageKey = '_sf2_attributes', $namespaceCharacter = '/')
        {
            $this->namespaceCharacter = $namespaceCharacter;
            parent::__construct($storageKey);
        }
        public function has($name)
        {
            $attributes = $this->resolveAttributePath($name);
            $name = $this->resolveKey($name);
            return array_key_exists($name, $attributes);
        }
        public function get($name, $default = null)
        {
            $attributes = $this->resolveAttributePath($name);
            $name = $this->resolveKey($name);
            return array_key_exists($name, $attributes) ? $attributes[$name] : $default;
        }
        public function set($name, $value)
        {
            $attributes = & $this->resolveAttributePath($name, true);
            $name = $this->resolveKey($name);
            $attributes[$name] = $value;
        }
        public function remove($name)
        {
            $retval = null;
            $attributes = & $this->resolveAttributePath($name);
            $name = $this->resolveKey($name);
            if (array_key_exists($name, $attributes)) {
                $retval = $attributes[$name];
                unset($attributes[$name]);
            }
            return $retval;
        }
        protected function &resolveAttributePath($name, $writeContext = false)
        {
            $array = & $this->attributes;
            $name = (strpos($name, $this->namespaceCharacter) === 0) ? substr($name, 1) : $name;
            if (!$name) {
                return $array;
            }
            $parts = explode($this->namespaceCharacter, $name);
            if (count($parts) < 2) {
                if (!$writeContext) {
                    return $array;
                }
                $array[$parts[0]] = array();
                return $array;
            }
            unset($parts[count($parts) - 1]);
            foreach ($parts as $part) {
                if (!array_key_exists($part, $array)) {
                    if (!$writeContext) {
                        return $array;
                    }
                    $array[$part] = array();
                }
                $array = & $array[$part];
            }
            return $array;
        }
        protected function resolveKey($name)
        {
            if (strpos($name, $this->namespaceCharacter) !== false) {
                $name = substr($name, strrpos($name, $this->namespaceCharacter) + 1, strlen($name));
            }
            return $name;
        }
    }
}
namespace Symfony\Component\HttpFoundation\File\Exception
{
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException.php
     */
    class AccessDeniedException extends FileException
    {
        public function __construct($path)
        {
            parent::__construct(sprintf('The file %s could not be accessed', $path));
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\File\Exception\FileException.php
     */
    class FileException extends \RuntimeException
    {
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException.php
     */
    class FileNotFoundException extends FileException
    {
        public function __construct($path)
        {
            parent::__construct(sprintf('The file "%s" does not exist', $path));
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException.php
     */
    class UnexpectedTypeException extends FileException
    {
        public function __construct($value, $expectedType)
        {
            parent::__construct(sprintf('Expected argument of type %s, %s given', $expectedType, is_object($value) ? get_class($value) : gettype($value)));
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\File\Exception\UploadException.php
     */
    class UploadException extends FileException
    {
    }
}
namespace Symfony\Component\HttpFoundation {
    use Symfony\Component\HttpFoundation\File\UploadedFile;
    use Symfony\Component\HttpFoundation\Session\SessionInterface;
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\ParameterBag.php
     */
    class ParameterBag implements \IteratorAggregate, \Countable
    {
        protected $parameters;
        public function __construct(array $parameters = array())
        {
            $this->parameters = $parameters;
        }
        public function all()
        {
            return $this->parameters;
        }
        public function keys()
        {
            return array_keys($this->parameters);
        }
        public function replace(array $parameters = array())
        {
            $this->parameters = $parameters;
        }
        public function add(array $parameters = array())
        {
            $this->parameters = array_replace($this->parameters, $parameters);
        }
        public function get($path, $default = null, $deep = false)
        {
            if (!$deep || false === $pos = strpos($path, '[')) {
                return array_key_exists($path, $this->parameters) ? $this->parameters[$path] : $default;
            }
            $root = substr($path, 0, $pos);
            if (!array_key_exists($root, $this->parameters)) {
                return $default;
            }
            $value = $this->parameters[$root];
            $currentKey = null;
            for ($i = $pos, $c = strlen($path); $i < $c; $i++) {
                $char = $path[$i];
                if ('[' === $char) {
                    if (null !== $currentKey) {
                        throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "[" at position %d.', $i));
                    }
                    $currentKey = '';
                } elseif (']' === $char) {
                    if (null === $currentKey) {
                        throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "]" at position %d.', $i));
                    }
                    if (!is_array($value) || !array_key_exists($currentKey, $value)) {
                        return $default;
                    }
                    $value = $value[$currentKey];
                    $currentKey = null;
                } else {
                    if (null === $currentKey) {
                        throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "%s" at position %d.', $char, $i));
                    }
                    $currentKey.= $char;
                }
            }
            if (null !== $currentKey) {
                throw new \InvalidArgumentException(sprintf('Malformed path. Path must end with "]".'));
            }
            return $value;
        }
        public function set($key, $value)
        {
            $this->parameters[$key] = $value;
        }
        public function has($key)
        {
            return array_key_exists($key, $this->parameters);
        }
        public function remove($key)
        {
            unset($this->parameters[$key]);
        }
        public function getAlpha($key, $default = '', $deep = false)
        {
            return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default, $deep));
        }
        public function getAlnum($key, $default = '', $deep = false)
        {
            return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default, $deep));
        }
        public function getDigits($key, $default = '', $deep = false)
        {
            return str_replace(array('-', '+'), '', $this->filter($key, $default, $deep, FILTER_SANITIZE_NUMBER_INT));
        }
        public function getInt($key, $default = 0, $deep = false)
        {
            return (int)$this->get($key, $default, $deep);
        }
        public function filter($key, $default = null, $deep = false, $filter = FILTER_DEFAULT, $options = array())
        {
            $value = $this->get($key, $default, $deep);
            if (!is_array($options) && $options) {
                $options = array('flags' => $options);
            }
            if (is_array($value) && !isset($options['flags'])) {
                $options['flags'] = FILTER_REQUIRE_ARRAY;
            }
            return filter_var($value, $filter, $options);
        }
        public function getIterator()
        {
            return new \ArrayIterator($this->parameters);
        }
        public function count()
        {
            return count($this->parameters);
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\ApacheRequest.php
     */
    class ApacheRequest extends Request
    {
        protected function prepareRequestUri()
        {
            return $this->server->get('REQUEST_URI');
        }
        protected function prepareBaseUrl()
        {
            $baseUrl = $this->server->get('SCRIPT_NAME');
            if (false === strpos($this->server->get('REQUEST_URI'), $baseUrl)) {
                return rtrim(dirname($baseUrl), '/\\');
            }
            return $baseUrl;
        }
        protected function preparePathInfo()
        {
            return $this->server->get('PATH_INFO') ? : substr($this->prepareRequestUri(), strlen($this->prepareBaseUrl())) ? : '/';
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Cookie.php
     */
    class Cookie
    {
        protected $name;
        protected $value;
        protected $domain;
        protected $expire;
        protected $path;
        protected $secure;
        protected $httpOnly;
        public function __construct($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
        {
            if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
                throw new \InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
            }
            if (empty($name)) {
                throw new \InvalidArgumentException('The cookie name cannot be empty.');
            }
            if ($expire instanceof \DateTime) {
                $expire = $expire->format('U');
            } elseif (!is_numeric($expire)) {
                $expire = strtotime($expire);
                if (false === $expire || - 1 === $expire) {
                    throw new \InvalidArgumentException('The cookie expiration time is not valid.');
                }
            }
            $this->name = $name;
            $this->value = $value;
            $this->domain = $domain;
            $this->expire = $expire;
            $this->path = empty($path) ? '/' : $path;
            $this->secure = (Boolean)$secure;
            $this->httpOnly = (Boolean)$httpOnly;
        }
        public function __toString()
        {
            $str = urlencode($this->getName()) . '=';
            if ('' === (string)$this->getValue()) {
                $str.= 'deleted; expires=' . gmdate("D, d-M-Y H:i:s T", time() - 31536001);
            } else {
                $str.= urlencode($this->getValue());
                if ($this->getExpiresTime() !== 0) {
                    $str.= '; expires=' . gmdate("D, d-M-Y H:i:s T", $this->getExpiresTime());
                }
            }
            if ('/' !== $this->path) {
                $str.= '; path=' . $this->path;
            }
            if (null !== $this->getDomain()) {
                $str.= '; domain=' . $this->getDomain();
            }
            if (true === $this->isSecure()) {
                $str.= '; secure';
            }
            if (true === $this->isHttpOnly()) {
                $str.= '; httponly';
            }
            return $str;
        }
        public function getName()
        {
            return $this->name;
        }
        public function getValue()
        {
            return $this->value;
        }
        public function getDomain()
        {
            return $this->domain;
        }
        public function getExpiresTime()
        {
            return $this->expire;
        }
        public function getPath()
        {
            return $this->path;
        }
        public function isSecure()
        {
            return $this->secure;
        }
        public function isHttpOnly()
        {
            return $this->httpOnly;
        }
        public function isCleared()
        {
            return $this->expire < time();
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\FileBag.php
     */
    class FileBag extends ParameterBag
    {
        private static $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
        public function __construct(array $parameters = array())
        {
            $this->replace($parameters);
        }
        public function replace(array $files = array())
        {
            $this->parameters = array();
            $this->add($files);
        }
        public function set($key, $value)
        {
            if (!is_array($value) && !$value instanceof UploadedFile) {
                throw new \InvalidArgumentException('An uploaded file must be an array or an instance of UploadedFile.');
            }
            parent::set($key, $this->convertFileInformation($value));
        }
        public function add(array $files = array())
        {
            foreach ($files as $key => $file) {
                $this->set($key, $file);
            }
        }
        protected function convertFileInformation($file)
        {
            if ($file instanceof UploadedFile) {
                return $file;
            }
            $file = $this->fixPhpFilesArray($file);
            if (is_array($file)) {
                $keys = array_keys($file);
                sort($keys);
                if ($keys == self::$fileKeys) {
                    if (UPLOAD_ERR_NO_FILE == $file['error']) {
                        $file = null;
                    } else {
                        $file = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);
                    }
                } else {
                    $file = array_map(array($this, 'convertFileInformation'), $file);
                }
            }
            return $file;
        }
        protected function fixPhpFilesArray($data)
        {
            if (!is_array($data)) {
                return $data;
            }
            $keys = array_keys($data);
            sort($keys);
            if (self::$fileKeys != $keys || !isset($data['name']) || !is_array($data['name'])) {
                return $data;
            }
            $files = $data;
            foreach (self::$fileKeys as $k) {
                unset($files[$k]);
            }
            foreach (array_keys($data['name']) as $key) {
                $files[$key] = $this->fixPhpFilesArray(array('error' => $data['error'][$key], 'name' => $data['name'][$key], 'type' => $data['type'][$key], 'tmp_name' => $data['tmp_name'][$key], 'size' => $data['size'][$key]));
            }
            return $files;
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\HeaderBag.php
     */
    class HeaderBag implements \IteratorAggregate, \Countable
    {
        protected $headers;
        protected $cacheControl;
        public function __construct(array $headers = array())
        {
            $this->cacheControl = array();
            $this->headers = array();
            foreach ($headers as $key => $values) {
                $this->set($key, $values);
            }
        }
        public function __toString()
        {
            if (!$this->headers) {
                return '';
            }
            $max = max(array_map('strlen', array_keys($this->headers))) + 1;
            $content = '';
            ksort($this->headers);
            foreach ($this->headers as $name => $values) {
                $name = implode('-', array_map('ucfirst', explode('-', $name)));
                foreach ($values as $value) {
                    $content.= sprintf("%-{$max}s %s\r\n", $name . ':', $value);
                }
            }
            return $content;
        }
        public function all()
        {
            return $this->headers;
        }
        public function keys()
        {
            return array_keys($this->headers);
        }
        public function replace(array $headers = array())
        {
            $this->headers = array();
            $this->add($headers);
        }
        public function add(array $headers)
        {
            foreach ($headers as $key => $values) {
                $this->set($key, $values);
            }
        }
        public function get($key, $default = null, $first = true)
        {
            $key = strtr(strtolower($key), '_', '-');
            if (!array_key_exists($key, $this->headers)) {
                if (null === $default) {
                    return $first ? null : array();
                }
                return $first ? $default : array($default);
            }
            if ($first) {
                return count($this->headers[$key]) ? $this->headers[$key][0] : $default;
            }
            return $this->headers[$key];
        }
        public function set($key, $values, $replace = true)
        {
            $key = strtr(strtolower($key), '_', '-');
            $values = array_values((array)$values);
            if (true === $replace || !isset($this->headers[$key])) {
                $this->headers[$key] = $values;
            } else {
                $this->headers[$key] = array_merge($this->headers[$key], $values);
            }
            if ('cache-control' === $key) {
                $this->cacheControl = $this->parseCacheControl($values[0]);
            }
        }
        public function has($key)
        {
            return array_key_exists(strtr(strtolower($key), '_', '-'), $this->headers);
        }
        public function contains($key, $value)
        {
            return in_array($value, $this->get($key, null, false));
        }
        public function remove($key)
        {
            $key = strtr(strtolower($key), '_', '-');
            unset($this->headers[$key]);
            if ('cache-control' === $key) {
                $this->cacheControl = array();
            }
        }
        public function getDate($key, \DateTime $default = null)
        {
            if (null === $value = $this->get($key)) {
                return $default;
            }
            if (false === $date = \DateTime::createFromFormat(DATE_RFC2822, $value)) {
                throw new \RuntimeException(sprintf('The %s HTTP header is not parseable (%s).', $key, $value));
            }
            return $date;
        }
        public function addCacheControlDirective($key, $value = true)
        {
            $this->cacheControl[$key] = $value;
            $this->set('Cache-Control', $this->getCacheControlHeader());
        }
        public function hasCacheControlDirective($key)
        {
            return array_key_exists($key, $this->cacheControl);
        }
        public function getCacheControlDirective($key)
        {
            return array_key_exists($key, $this->cacheControl) ? $this->cacheControl[$key] : null;
        }
        public function removeCacheControlDirective($key)
        {
            unset($this->cacheControl[$key]);
            $this->set('Cache-Control', $this->getCacheControlHeader());
        }
        public function getIterator()
        {
            return new \ArrayIterator($this->headers);
        }
        public function count()
        {
            return count($this->headers);
        }
        protected function getCacheControlHeader()
        {
            $parts = array();
            ksort($this->cacheControl);
            foreach ($this->cacheControl as $key => $value) {
                if (true === $value) {
                    $parts[] = $key;
                } else {
                    if (preg_match('#[^a-zA-Z0-9._-]#', $value)) {
                        $value = '"' . $value . '"';
                    }
                    $parts[] = "$key=$value";
                }
            }
            return implode(', ', $parts);
        }
        protected function parseCacheControl($header)
        {
            $cacheControl = array();
            preg_match_all('#([a-zA-Z][a-zA-Z_-]*)\s*(?:=(?:"([^"]*)"|([^ \t",;]*)))?#', $header, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $cacheControl[strtolower($match[1]) ] = isset($match[3]) ? $match[3] : (isset($match[2]) ? $match[2] : true);
            }
            return $cacheControl;
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\JsonResponse.php
     */
    class JsonResponse extends Response
    {
        protected $data;
        protected $callback;
        public function __construct($data = null, $status = 200, $headers = array())
        {
            parent::__construct('', $status, $headers);
            if (null === $data) {
                $data = new \ArrayObject();
            }
            $this->setData($data);
        }
        public static function create($data = null, $status = 200, $headers = array())
        {
            return new static ($data, $status, $headers);
        }
        public function setCallback($callback = null)
        {
            if (null !== $callback) {
                $pattern = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';
                $parts = explode('.', $callback);
                foreach ($parts as $part) {
                    if (!preg_match($pattern, $part)) {
                        throw new \InvalidArgumentException('The callback name is not valid.');
                    }
                }
            }
            $this->callback = $callback;
            return $this->update();
        }
        public function setData($data = array())
        {
            $this->data = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
            return $this->update();
        }
        protected function update()
        {
            if (null !== $this->callback) {
                $this->headers->set('Content-Type', 'text/javascript');
                return $this->setContent(sprintf('%s(%s);', $this->callback, $this->data));
            }
            if (!$this->headers->has('Content-Type') || 'text/javascript' === $this->headers->get('Content-Type')) {
                $this->headers->set('Content-Type', 'application/json');
            }
            return $this->setContent($this->data);
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\LaravelRequest.php
     */
    class LaravelRequest extends Request
    {
        static public function createFromGlobals()
        {
            $request = new static ($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);
            if ((0 === strpos($request->server->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded') || (0 === strpos($request->server->get('HTTP_CONTENT_TYPE'), 'application/x-www-form-urlencoded'))) && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))) {
                parse_str($request->getContent(), $data);
                if (magic_quotes()) $data = array_strip_slashes($data);
                $request->request = new ParameterBag($data);
            }
            return $request;
        }
        public function getRootUrl()
        {
            return $this->getScheme() . '://' . $this->getHttpHost() . $this->getBasePath();
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\LaravelResponse.php
     */
    class LaravelResponse extends Response
    {
        public function send()
        {
            $this->sendHeaders();
            $this->sendContent();
            return $this;
        }
        public function finish()
        {
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\RedirectResponse.php
     */
    class RedirectResponse extends Response
    {
        protected $targetUrl;
        public function __construct($url, $status = 302, $headers = array())
        {
            if (empty($url)) {
                throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
            }
            parent::__construct('', $status, $headers);
            $this->setTargetUrl($url);
            if (!$this->isRedirect()) {
                throw new \InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $status));
            }
        }
        public static function create($url = '', $status = 302, $headers = array())
        {
            return new static ($url, $status, $headers);
        }
        public function getTargetUrl()
        {
            return $this->targetUrl;
        }
        public function setTargetUrl($url)
        {
            if (empty($url)) {
                throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
            }
            $this->targetUrl = $url;
            $this->setContent(sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="refresh" content="1;url=%1$s" />

        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')));
            $this->headers->set('Location', $url);
            return $this;
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Request.php
     */
    class Request
    {
        const HEADER_CLIENT_IP = 'client_ip';
        const HEADER_CLIENT_HOST = 'client_host';
        const HEADER_CLIENT_PROTO = 'client_proto';
        const HEADER_CLIENT_PORT = 'client_port';
        protected static $trustProxy = false;
        protected static $trustedProxies = array();
        protected static $trustedHeaders = array(self::HEADER_CLIENT_IP => 'X_FORWARDED_FOR', self::HEADER_CLIENT_HOST => 'X_FORWARDED_HOST', self::HEADER_CLIENT_PROTO => 'X_FORWARDED_PROTO', self::HEADER_CLIENT_PORT => 'X_FORWARDED_PORT',);
        protected static $httpMethodParameterOverride = false;
        public $attributes;
        public $request;
        public $query;
        public $server;
        public $files;
        public $cookies;
        public $headers;
        protected $content;
        protected $languages;
        protected $charsets;
        protected $acceptableContentTypes;
        protected $pathInfo;
        protected $requestUri;
        protected $baseUrl;
        protected $basePath;
        protected $method;
        protected $format;
        protected $session;
        protected $locale;
        protected $defaultLocale = 'en';
        protected static $formats;
        public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
        {
            $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
        }
        public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
        {
            $this->request = new ParameterBag($request);
            $this->query = new ParameterBag($query);
            $this->attributes = new ParameterBag($attributes);
            $this->cookies = new ParameterBag($cookies);
            $this->files = new FileBag($files);
            $this->server = new ServerBag($server);
            $this->headers = new HeaderBag($this->server->getHeaders());
            $this->content = $content;
            $this->languages = null;
            $this->charsets = null;
            $this->acceptableContentTypes = null;
            $this->pathInfo = null;
            $this->requestUri = null;
            $this->baseUrl = null;
            $this->basePath = null;
            $this->method = null;
            $this->format = null;
        }
        public static function createFromGlobals()
        {
            $request = new static ($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);
            if (0 === strpos($request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded') && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))) {
                parse_str($request->getContent(), $data);
                $request->request = new ParameterBag($data);
            }
            return $request;
        }
        public static function create($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null)
        {
            $server = array_replace(array('SERVER_NAME' => 'localhost', 'SERVER_PORT' => 80, 'HTTP_HOST' => 'localhost', 'HTTP_USER_AGENT' => 'Symfony/2.X', 'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5', 'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7', 'REMOTE_ADDR' => '127.0.0.1', 'SCRIPT_NAME' => '', 'SCRIPT_FILENAME' => '', 'SERVER_PROTOCOL' => 'HTTP/1.1', 'REQUEST_TIME' => time(),), $server);
            $server['PATH_INFO'] = '';
            $server['REQUEST_METHOD'] = strtoupper($method);
            $components = parse_url($uri);
            if (isset($components['host'])) {
                $server['SERVER_NAME'] = $components['host'];
                $server['HTTP_HOST'] = $components['host'];
            }
            if (isset($components['scheme'])) {
                if ('https' === $components['scheme']) {
                    $server['HTTPS'] = 'on';
                    $server['SERVER_PORT'] = 443;
                } else {
                    unset($server['HTTPS']);
                    $server['SERVER_PORT'] = 80;
                }
            }
            if (isset($components['port'])) {
                $server['SERVER_PORT'] = $components['port'];
                $server['HTTP_HOST'] = $server['HTTP_HOST'] . ':' . $components['port'];
            }
            if (isset($components['user'])) {
                $server['PHP_AUTH_USER'] = $components['user'];
            }
            if (isset($components['pass'])) {
                $server['PHP_AUTH_PW'] = $components['pass'];
            }
            if (!isset($components['path'])) {
                $components['path'] = '/';
            }
            switch (strtoupper($method)) {
            case 'POST':
            case 'PUT':
            case 'DELETE':
                if (!isset($server['CONTENT_TYPE'])) {
                    $server['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
                }
            case 'PATCH':
                $request = $parameters;
                $query = array();
                break;

            default:
                $request = array();
                $query = $parameters;
                break;
            }
            if (isset($components['query'])) {
                parse_str(html_entity_decode($components['query']), $qs);
                $query = array_replace($qs, $query);
            }
            $queryString = http_build_query($query, '', '&');
            $server['REQUEST_URI'] = $components['path'] . ('' !== $queryString ? '?' . $queryString : '');
            $server['QUERY_STRING'] = $queryString;
            return new static ($query, $request, array(), $cookies, $files, $server, $content);
        }
        public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
        {
            $dup = clone $this;
            if ($query !== null) {
                $dup->query = new ParameterBag($query);
            }
            if ($request !== null) {
                $dup->request = new ParameterBag($request);
            }
            if ($attributes !== null) {
                $dup->attributes = new ParameterBag($attributes);
            }
            if ($cookies !== null) {
                $dup->cookies = new ParameterBag($cookies);
            }
            if ($files !== null) {
                $dup->files = new FileBag($files);
            }
            if ($server !== null) {
                $dup->server = new ServerBag($server);
                $dup->headers = new HeaderBag($dup->server->getHeaders());
            }
            $dup->languages = null;
            $dup->charsets = null;
            $dup->acceptableContentTypes = null;
            $dup->pathInfo = null;
            $dup->requestUri = null;
            $dup->baseUrl = null;
            $dup->basePath = null;
            $dup->method = null;
            $dup->format = null;
            return $dup;
        }
        public function __clone()
        {
            $this->query = clone $this->query;
            $this->request = clone $this->request;
            $this->attributes = clone $this->attributes;
            $this->cookies = clone $this->cookies;
            $this->files = clone $this->files;
            $this->server = clone $this->server;
            $this->headers = clone $this->headers;
        }
        public function __toString()
        {
            return sprintf('%s %s %s', $this->getMethod(), $this->getRequestUri(), $this->server->get('SERVER_PROTOCOL')) . "\r\n" . $this->headers . "\r\n" . $this->getContent();
        }
        public function overrideGlobals()
        {
            $_GET = $this->query->all();
            $_POST = $this->request->all();
            $_SERVER = $this->server->all();
            $_COOKIE = $this->cookies->all();
            foreach ($this->headers->all() as $key => $value) {
                $key = strtoupper(str_replace('-', '_', $key));
                if (in_array($key, array('CONTENT_TYPE', 'CONTENT_LENGTH'))) {
                    $_SERVER[$key] = implode(', ', $value);
                } else {
                    $_SERVER['HTTP_' . $key] = implode(', ', $value);
                }
            }
            $request = array('g' => $_GET, 'p' => $_POST, 'c' => $_COOKIE);
            $requestOrder = ini_get('request_order') ? : ini_get('variable_order');
            $requestOrder = preg_replace('#[^cgp]#', '', strtolower($requestOrder)) ? : 'gp';
            $_REQUEST = array();
            foreach (str_split($requestOrder) as $order) {
                $_REQUEST = array_merge($_REQUEST, $request[$order]);
            }
        }
        public static function trustProxyData()
        {
            trigger_error('trustProxyData() is deprecated since version 2.0 and will be removed in 2.3. Use setTrustedProxies() instead.', E_USER_DEPRECATED);
            self::$trustProxy = true;
        }
        public static function setTrustedProxies(array $proxies)
        {
            self::$trustedProxies = $proxies;
            self::$trustProxy = $proxies ? true : false;
        }
        public static function getTrustedProxies()
        {
            return self::$trustedProxies;
        }
        public static function setTrustedHeaderName($key, $value)
        {
            if (!array_key_exists($key, self::$trustedHeaders)) {
                throw new \InvalidArgumentException(sprintf('Unable to set the trusted header name for key "%s".', $key));
            }
            self::$trustedHeaders[$key] = $value;
        }
        public static function isProxyTrusted()
        {
            return self::$trustProxy;
        }
        public static function normalizeQueryString($qs)
        {
            if ('' == $qs) {
                return '';
            }
            $parts = array();
            $order = array();
            foreach (explode('&', $qs) as $param) {
                if ('' === $param || '=' === $param[0]) {
                    continue;
                }
                $keyValuePair = explode('=', $param, 2);
                $parts[] = isset($keyValuePair[1]) ? rawurlencode(urldecode($keyValuePair[0])) . '=' . rawurlencode(urldecode($keyValuePair[1])) : rawurlencode(urldecode($keyValuePair[0]));
                $order[] = urldecode($keyValuePair[0]);
            }
            array_multisort($order, SORT_ASC, $parts);
            return implode('&', $parts);
        }
        public static function enableHttpMethodParameterOverride()
        {
            self::$httpMethodParameterOverride = true;
        }
        public static function getHttpMethodParameterOverride()
        {
            return self::$httpMethodParameterOverride;
        }
        public function get($key, $default = null, $deep = false)
        {
            return $this->query->get($key, $this->attributes->get($key, $this->request->get($key, $default, $deep), $deep), $deep);
        }
        public function getSession()
        {
            return $this->session;
        }
        public function hasPreviousSession()
        {
            return $this->hasSession() && $this->cookies->has($this->session->getName());
        }
        public function hasSession()
        {
            return null !== $this->session;
        }
        public function setSession(SessionInterface $session)
        {
            $this->session = $session;
        }
        public function getClientIp()
        {
            $ip = $this->server->get('REMOTE_ADDR');
            if (!self::$trustProxy) {
                return $ip;
            }
            if (!self::$trustedHeaders[self::HEADER_CLIENT_IP] || !$this->headers->has(self::$trustedHeaders[self::HEADER_CLIENT_IP])) {
                return $ip;
            }
            $clientIps = array_map('trim', explode(',', $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_IP])));
            $clientIps[] = $ip;
            $trustedProxies = self::$trustProxy && !self::$trustedProxies ? array($ip) : self::$trustedProxies;
            $clientIps = array_diff($clientIps, $trustedProxies);
            return array_pop($clientIps);
        }
        public function getScriptName()
        {
            return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', ''));
        }
        public function getPathInfo()
        {
            if (null === $this->pathInfo) {
                $this->pathInfo = $this->preparePathInfo();
            }
            return $this->pathInfo;
        }
        public function getBasePath()
        {
            if (null === $this->basePath) {
                $this->basePath = $this->prepareBasePath();
            }
            return $this->basePath;
        }
        public function getBaseUrl()
        {
            if (null === $this->baseUrl) {
                $this->baseUrl = $this->prepareBaseUrl();
            }
            return $this->baseUrl;
        }
        public function getScheme()
        {
            return $this->isSecure() ? 'https' : 'http';
        }
        public function getPort()
        {
            if (self::$trustProxy && self::$trustedHeaders[self::HEADER_CLIENT_PORT] && $port = $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_PORT])) {
                return $port;
            }
            return $this->server->get('SERVER_PORT');
        }
        public function getUser()
        {
            return $this->server->get('PHP_AUTH_USER');
        }
        public function getPassword()
        {
            return $this->server->get('PHP_AUTH_PW');
        }
        public function getUserInfo()
        {
            $userinfo = $this->getUser();
            $pass = $this->getPassword();
            if ('' != $pass) {
                $userinfo.= ":$pass";
            }
            return $userinfo;
        }
        public function getHttpHost()
        {
            $scheme = $this->getScheme();
            $port = $this->getPort();
            if (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443)) {
                return $this->getHost();
            }
            return $this->getHost() . ':' . $port;
        }
        public function getRequestUri()
        {
            if (null === $this->requestUri) {
                $this->requestUri = $this->prepareRequestUri();
            }
            return $this->requestUri;
        }
        public function getSchemeAndHttpHost()
        {
            return $this->getScheme() . '://' . $this->getHttpHost();
        }
        public function getUri()
        {
            if (null !== $qs = $this->getQueryString()) {
                $qs = '?' . $qs;
            }
            return $this->getSchemeAndHttpHost() . $this->getBaseUrl() . $this->getPathInfo() . $qs;
        }
        public function getUriForPath($path)
        {
            return $this->getSchemeAndHttpHost() . $this->getBaseUrl() . $path;
        }
        public function getQueryString()
        {
            $qs = static ::normalizeQueryString($this->server->get('QUERY_STRING'));
            return '' === $qs ? null : $qs;
        }
        public function isSecure()
        {
            if (self::$trustProxy && self::$trustedHeaders[self::HEADER_CLIENT_PROTO] && $proto = $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_PROTO])) {
                return in_array(strtolower($proto), array('https', 'on', '1'));
            }
            return 'on' == strtolower($this->server->get('HTTPS')) || 1 == $this->server->get('HTTPS');
        }
        public function getHost()
        {
            if (self::$trustProxy && self::$trustedHeaders[self::HEADER_CLIENT_HOST] && $host = $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_HOST])) {
                $elements = explode(',', $host);
                $host = $elements[count($elements) - 1];
            } elseif (!$host = $this->headers->get('HOST')) {
                if (!$host = $this->server->get('SERVER_NAME')) {
                    $host = $this->server->get('SERVER_ADDR', '');
                }
            }
            $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));
            if ($host && !preg_match('/^\[?(?:[a-zA-Z0-9-:\]_]+\.?)+$/', $host)) {
                throw new \UnexpectedValueException('Invalid Host');
            }
            return $host;
        }
        public function setMethod($method)
        {
            $this->method = null;
            $this->server->set('REQUEST_METHOD', $method);
        }
        public function getMethod()
        {
            if (null === $this->method) {
                $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
                if ('POST' === $this->method) {
                    if ($method = $this->headers->get('X-HTTP-METHOD-OVERRIDE')) {
                        $this->method = strtoupper($method);
                    } elseif (self::$httpMethodParameterOverride) {
                        $this->method = strtoupper($this->request->get('_method', $this->query->get('_method', 'POST')));
                    }
                }
            }
            return $this->method;
        }
        public function getRealMethod()
        {
            return strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
        }
        public function getMimeType($format)
        {
            if (null === static ::$formats) {
                static ::initializeFormats();
            }
            return isset(static ::$formats[$format]) ? static ::$formats[$format][0] : null;
        }
        public function getFormat($mimeType)
        {
            if (false !== $pos = strpos($mimeType, ';')) {
                $mimeType = substr($mimeType, 0, $pos);
            }
            if (null === static ::$formats) {
                static ::initializeFormats();
            }
            foreach (static ::$formats as $format => $mimeTypes) {
                if (in_array($mimeType, (array)$mimeTypes)) {
                    return $format;
                }
            }
            return null;
        }
        public function setFormat($format, $mimeTypes)
        {
            if (null === static ::$formats) {
                static ::initializeFormats();
            }
            static ::$formats[$format] = is_array($mimeTypes) ? $mimeTypes : array($mimeTypes);
        }
        public function getRequestFormat($default = 'html')
        {
            if (null === $this->format) {
                $this->format = $this->get('_format', $default);
            }
            return $this->format;
        }
        public function setRequestFormat($format)
        {
            $this->format = $format;
        }
        public function getContentType()
        {
            return $this->getFormat($this->headers->get('CONTENT_TYPE'));
        }
        public function setDefaultLocale($locale)
        {
            $this->defaultLocale = $locale;
            if (null === $this->locale) {
                $this->setPhpDefaultLocale($locale);
            }
        }
        public function setLocale($locale)
        {
            $this->setPhpDefaultLocale($this->locale = $locale);
        }
        public function getLocale()
        {
            return null === $this->locale ? $this->defaultLocale : $this->locale;
        }
        public function isMethod($method)
        {
            return $this->getMethod() === strtoupper($method);
        }
        public function isMethodSafe()
        {
            return in_array($this->getMethod(), array('GET', 'HEAD'));
        }
        public function getContent($asResource = false)
        {
            if (false === $this->content || (true === $asResource && null !== $this->content)) {
                throw new \LogicException('getContent() can only be called once when using the resource return type.');
            }
            if (true === $asResource) {
                $this->content = false;
                return fopen('php://input', 'rb');
            }
            if (null === $this->content) {
                $this->content = file_get_contents('php://input');
            }
            return $this->content;
        }
        public function getETags()
        {
            return preg_split('/\s*,\s*/', $this->headers->get('if_none_match'), null, PREG_SPLIT_NO_EMPTY);
        }
        public function isNoCache()
        {
            return $this->headers->hasCacheControlDirective('no-cache') || 'no-cache' == $this->headers->get('Pragma');
        }
        public function getPreferredLanguage(array $locales = null)
        {
            $preferredLanguages = $this->getLanguages();
            if (empty($locales)) {
                return isset($preferredLanguages[0]) ? $preferredLanguages[0] : null;
            }
            if (!$preferredLanguages) {
                return $locales[0];
            }
            $preferredLanguages = array_values(array_intersect($preferredLanguages, $locales));
            return isset($preferredLanguages[0]) ? $preferredLanguages[0] : $locales[0];
        }
        public function getLanguages()
        {
            if (null !== $this->languages) {
                return $this->languages;
            }
            $languages = AcceptHeader::fromString($this->headers->get('Accept-Language'))->all();
            $this->languages = array();
            foreach (array_keys($languages) as $lang) {
                if (strstr($lang, '-')) {
                    $codes = explode('-', $lang);
                    if ($codes[0] == 'i') {
                        if (count($codes) > 1) {
                            $lang = $codes[1];
                        }
                    } else {
                        for ($i = 0, $max = count($codes); $i < $max; $i++) {
                            if ($i == 0) {
                                $lang = strtolower($codes[0]);
                            } else {
                                $lang.= '_' . strtoupper($codes[$i]);
                            }
                        }
                    }
                }
                $this->languages[] = $lang;
            }
            return $this->languages;
        }
        public function getCharsets()
        {
            if (null !== $this->charsets) {
                return $this->charsets;
            }
            return $this->charsets = array_keys(AcceptHeader::fromString($this->headers->get('Accept-Charset'))->all());
        }
        public function getAcceptableContentTypes()
        {
            if (null !== $this->acceptableContentTypes) {
                return $this->acceptableContentTypes;
            }
            return $this->acceptableContentTypes = array_keys(AcceptHeader::fromString($this->headers->get('Accept'))->all());
        }
        public function isXmlHttpRequest()
        {
            return 'XMLHttpRequest' == $this->headers->get('X-Requested-With');
        }
        public function splitHttpAcceptHeader($header)
        {
            trigger_error('splitHttpAcceptHeader() is deprecated since version 2.2 and will be removed in 2.3.', E_USER_DEPRECATED);
            $headers = array();
            foreach (AcceptHeader::fromString($header)->all() as $item) {
                $key = $item->getValue();
                foreach ($item->getAttributes() as $name => $value) {
                    $key.= sprintf(';%s=%s', $name, $value);
                }
                $headers[$key] = $item->getQuality();
            }
            return $headers;
        }
        protected function prepareRequestUri()
        {
            $requestUri = '';
            if ($this->headers->has('X_ORIGINAL_URL') && false !== stripos(PHP_OS, 'WIN')) {
                $requestUri = $this->headers->get('X_ORIGINAL_URL');
                $this->headers->remove('X_ORIGINAL_URL');
            } elseif ($this->headers->has('X_REWRITE_URL') && false !== stripos(PHP_OS, 'WIN')) {
                $requestUri = $this->headers->get('X_REWRITE_URL');
                $this->headers->remove('X_REWRITE_URL');
            } elseif ($this->server->get('IIS_WasUrlRewritten') == '1' && $this->server->get('UNENCODED_URL') != '') {
                $requestUri = $this->server->get('UNENCODED_URL');
                $this->server->remove('UNENCODED_URL');
                $this->server->remove('IIS_WasUrlRewritten');
            } elseif ($this->server->has('REQUEST_URI')) {
                $requestUri = $this->server->get('REQUEST_URI');
                $schemeAndHttpHost = $this->getSchemeAndHttpHost();
                if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                    $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
                }
            } elseif ($this->server->has('ORIG_PATH_INFO')) {
                $requestUri = $this->server->get('ORIG_PATH_INFO');
                if ('' != $this->server->get('QUERY_STRING')) {
                    $requestUri.= '?' . $this->server->get('QUERY_STRING');
                }
                $this->server->remove('ORIG_PATH_INFO');
            }
            $this->server->set('REQUEST_URI', $requestUri);
            return $requestUri;
        }
        protected function prepareBaseUrl()
        {
            $filename = basename($this->server->get('SCRIPT_FILENAME'));
            if (basename($this->server->get('SCRIPT_NAME')) === $filename) {
                $baseUrl = $this->server->get('SCRIPT_NAME');
            } elseif (basename($this->server->get('PHP_SELF')) === $filename) {
                $baseUrl = $this->server->get('PHP_SELF');
            } elseif (basename($this->server->get('ORIG_SCRIPT_NAME')) === $filename) {
                $baseUrl = $this->server->get('ORIG_SCRIPT_NAME');
            } else {
                $path = $this->server->get('PHP_SELF', '');
                $file = $this->server->get('SCRIPT_FILENAME', '');
                $segs = explode('/', trim($file, '/'));
                $segs = array_reverse($segs);
                $index = 0;
                $last = count($segs);
                $baseUrl = '';
                do {
                    $seg = $segs[$index];
                    $baseUrl = '/' . $seg . $baseUrl;
                    ++$index;
                } while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
            }
            $requestUri = $this->getRequestUri();
            if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl)) {
                return $prefix;
            }
            if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, dirname($baseUrl))) {
                return rtrim($prefix, '/');
            }
            $truncatedRequestUri = $requestUri;
            if (($pos = strpos($requestUri, '?')) !== false) {
                $truncatedRequestUri = substr($requestUri, 0, $pos);
            }
            $basename = basename($baseUrl);
            if (empty($basename) || !strpos(rawurldecode($truncatedRequestUri), $basename)) {
                return '';
            }
            if ((strlen($requestUri) >= strlen($baseUrl)) && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0))) {
                $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
            }
            return rtrim($baseUrl, '/');
        } protected function prepareBasePath()
        {
            $filename = basename($this->server->get('SCRIPT_FILENAME'));
            $baseUrl = $this->getBaseUrl();
            if (empty($baseUrl)) {
                return '';
            }
            if (basename($baseUrl) === $filename) {
                $basePath = dirname($baseUrl);
            } else {
                $basePath = $baseUrl;
            }
            if ('\\' === DIRECTORY_SEPARATOR) {
                $basePath = str_replace('\\', '/', $basePath);
            }
            return rtrim($basePath, '/');
        }
        protected function preparePathInfo()
        {
            $baseUrl = $this->getBaseUrl();
            if (null === ($requestUri = $this->getRequestUri())) {
                return '/';
            }
            $pathInfo = '/';
            if ($pos = strpos($requestUri, '?')) {
                $requestUri = substr($requestUri, 0, $pos);
            }
            if ((null !== $baseUrl) && (false === ($pathInfo = substr($requestUri, strlen($baseUrl))))) {
                return '/';
            } elseif (null === $baseUrl) {
                return $requestUri;
            }
            return (string)$pathInfo;
        }
        protected static function initializeFormats()
        {
            static ::$formats = array('html' => array('text/html', 'application/xhtml+xml'), 'txt' => array('text/plain'), 'js' => array('application/javascript', 'application/x-javascript', 'text/javascript'), 'css' => array('text/css'), 'json' => array('application/json', 'application/x-json'), 'xml' => array('text/xml', 'application/xml', 'application/x-xml'), 'rdf' => array('application/rdf+xml'), 'atom' => array('application/atom+xml'), 'rss' => array('application/rss+xml'),);
        }
        private function setPhpDefaultLocale($locale)
        {
            try {
                if (class_exists('Locale', false)) {
                    \Locale::setDefault($locale);
                }
            }
            catch(\Exception $e) {
            }
        }
        private function getUrlencodedPrefix($string, $prefix)
        {
            if (0 !== strpos(rawurldecode($string), $prefix)) {
                return false;
            }
            $len = strlen($prefix);
            if (preg_match("#^(%[[:xdigit:]]{2}|.){{$len}}#", $string, $match)) {
                return $match[0];
            }
            return false;
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\RequestMatcherInterface.php
     */
    interface RequestMatcherInterface {
        public function matches(Request $request);
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\RequestMatcher.php
     */
    class RequestMatcher implements RequestMatcherInterface
    {
        private $path;
        private $host;
        private $methods = array();
        private $ip;
        private $attributes = array();
        public function __construct($path = null, $host = null, $methods = null, $ip = null, array $attributes = array())
        {
            $this->matchPath($path);
            $this->matchHost($host);
            $this->matchMethod($methods);
            $this->matchIp($ip);
            foreach ($attributes as $k => $v) {
                $this->matchAttribute($k, $v);
            }
        }
        public function matchHost($regexp)
        {
            $this->host = $regexp;
        }
        public function matchPath($regexp)
        {
            $this->path = $regexp;
        }
        public function matchIp($ip)
        {
            $this->ip = $ip;
        }
        public function matchMethod($method)
        {
            $this->methods = array_map('strtoupper', (array)$method);
        }
        public function matchAttribute($key, $regexp)
        {
            $this->attributes[$key] = $regexp;
        }
        public function matches(Request $request)
        {
            if ($this->methods && !in_array($request->getMethod(), $this->methods)) {
                return false;
            }
            foreach ($this->attributes as $key => $pattern) {
                if (!preg_match('#' . str_replace('#', '\\#', $pattern) . '#', $request->attributes->get($key))) {
                    return false;
                }
            }
            if (null !== $this->path) {
                $path = str_replace('#', '\\#', $this->path);
                if (!preg_match('#' . $path . '#', rawurldecode($request->getPathInfo()))) {
                    return false;
                }
            }
            if (null !== $this->host && !preg_match('#' . str_replace('#', '\\#', $this->host) . '#i', $request->getHost())) {
                return false;
            }
            if (null !== $this->ip && !IpUtils::checkIp($request->getClientIp(), $this->ip)) {
                return false;
            }
            return true;
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Response.php
     */
    class Response
    {
        public $headers;
        protected $content;
        protected $version;
        protected $statusCode;
        protected $statusText;
        protected $charset;
        public static $statusTexts = array(100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing', 200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-Status', 208 => 'Already Reported', 226 => 'IM Used', 300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Reserved', 307 => 'Temporary Redirect', 308 => 'Permanent Redirect', 400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 418 => 'I\'m a teapot', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 425 => 'Reserved for WebDAV advanced collections expired proposal', 426 => 'Upgrade Required', 428 => 'Precondition Required', 429 => 'Too Many Requests', 431 => 'Request Header Fields Too Large', 500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 506 => 'Variant Also Negotiates (Experimental)', 507 => 'Insufficient Storage', 508 => 'Loop Detected', 510 => 'Not Extended', 511 => 'Network Authentication Required',);
        public function __construct($content = '', $status = 200, $headers = array())
        {
            $this->headers = new ResponseHeaderBag($headers);
            $this->setContent($content);
            $this->setStatusCode($status);
            $this->setProtocolVersion('1.0');
            if (!$this->headers->has('Date')) {
                $this->setDate(new \DateTime(null, new \DateTimeZone('UTC')));
            }
        }
        public static function create($content = '', $status = 200, $headers = array())
        {
            return new static ($content, $status, $headers);
        }
        public function __toString()
        {
            return sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText) . "\r\n" . $this->headers . "\r\n" . $this->getContent();
        }
        public function __clone()
        {
            $this->headers = clone $this->headers;
        }
        public function prepare(Request $request)
        {
            $headers = $this->headers;
            if ($this->isInformational() || in_array($this->statusCode, array(204, 304))) {
                $this->setContent(null);
            }
            if (!$headers->has('Content-Type')) {
                $format = $request->getRequestFormat();
                if (null !== $format && $mimeType = $request->getMimeType($format)) {
                    $headers->set('Content-Type', $mimeType);
                }
            }
            $charset = $this->charset ? : 'UTF-8';
            if (!$headers->has('Content-Type')) {
                $headers->set('Content-Type', 'text/html; charset=' . $charset);
            } elseif (0 === strpos($headers->get('Content-Type'), 'text/') && false === strpos($headers->get('Content-Type'), 'charset')) {
                $headers->set('Content-Type', $headers->get('Content-Type') . '; charset=' . $charset);
            }
            if ($headers->has('Transfer-Encoding')) {
                $headers->remove('Content-Length');
            }
            if ($request->isMethod('HEAD')) {
                $length = $headers->get('Content-Length');
                $this->setContent(null);
                if ($length) {
                    $headers->set('Content-Length', $length);
                }
            }
            if ('HTTP/1.0' != $request->server->get('SERVER_PROTOCOL')) {
                $this->setProtocolVersion('1.1');
            }
            if ('1.0' == $this->getProtocolVersion() && 'no-cache' == $this->headers->get('Cache-Control')) {
                $this->headers->set('pragma', 'no-cache');
                $this->headers->set('expires', -1);
            }
            if (false !== stripos($this->headers->get('Content-Disposition'), 'attachment') && preg_match('/MSIE (.*?);/i', $request->server->get('HTTP_USER_AGENT'), $match) == 1 && true === $request->isSecure()) {
                if (intval(preg_replace("/(MSIE )(.*?);/", "$2", $match[0])) < 9) {
                    $this->headers->remove('Cache-Control');
                }
            }
            return $this;
        }
        public function sendHeaders()
        {
            if (headers_sent()) {
                return $this;
            }
            header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText));
            foreach ($this->headers->allPreserveCase() as $name => $values) {
                foreach ($values as $value) {
                    header($name . ': ' . $value, false);
                }
            }
            foreach ($this->headers->getCookies() as $cookie) {
                setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
            }
            return $this;
        }
        public function sendContent()
        {
            echo $this->content;
            return $this;
        }
        public function send()
        {
            $this->sendHeaders();
            $this->sendContent();
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            } elseif ('cli' !== PHP_SAPI) {
                $previous = null;
                $obStatus = ob_get_status(1);
                while (($level = ob_get_level()) > 0 && $level !== $previous) {
                    $previous = $level;
                    if ($obStatus[$level - 1] && isset($obStatus[$level - 1]['del']) && $obStatus[$level - 1]['del']) {
                        ob_end_flush();
                    }
                }
                flush();
            }
            return $this;
        }
        public function setContent($content)
        {
            if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable(array($content, '__toString'))) {
                throw new \UnexpectedValueException('The Response content must be a string or object implementing __toString(), "' . gettype($content) . '" given.');
            }
            $this->content = (string)$content;
            return $this;
        }
        public function getContent()
        {
            return $this->content;
        }
        public function setProtocolVersion($version)
        {
            $this->version = $version;
            return $this;
        }
        public function getProtocolVersion()
        {
            return $this->version;
        }
        public function setStatusCode($code, $text = null)
        {
            $this->statusCode = $code = (int)$code;
            if ($this->isInvalid()) {
                throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
            }
            if (null === $text) {
                $this->statusText = isset(self::$statusTexts[$code]) ? self::$statusTexts[$code] : '';
                return $this;
            }
            if (false === $text) {
                $this->statusText = '';
                return $this;
            }
            $this->statusText = $text;
            return $this;
        }
        public function getStatusCode()
        {
            return $this->statusCode;
        }
        public function setCharset($charset)
        {
            $this->charset = $charset;
            return $this;
        }
        public function getCharset()
        {
            return $this->charset;
        }
        public function isCacheable()
        {
            if (!in_array($this->statusCode, array(200, 203, 300, 301, 302, 404, 410))) {
                return false;
            }
            if ($this->headers->hasCacheControlDirective('no-store') || $this->headers->getCacheControlDirective('private')) {
                return false;
            }
            return $this->isValidateable() || $this->isFresh();
        }
        public function isFresh()
        {
            return $this->getTtl() > 0;
        }
        public function isValidateable()
        {
            return $this->headers->has('Last-Modified') || $this->headers->has('ETag');
        }
        public function setPrivate()
        {
            $this->headers->removeCacheControlDirective('public');
            $this->headers->addCacheControlDirective('private');
            return $this;
        }
        public function setPublic()
        {
            $this->headers->addCacheControlDirective('public');
            $this->headers->removeCacheControlDirective('private');
            return $this;
        }
        public function mustRevalidate()
        {
            return $this->headers->hasCacheControlDirective('must-revalidate') || $this->headers->has('proxy-revalidate');
        }
        public function getDate()
        {
            return $this->headers->getDate('Date', new \DateTime());
        }
        public function setDate(\DateTime $date)
        {
            $date->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('Date', $date->format('D, d M Y H:i:s') . ' GMT');
            return $this;
        }
        public function getAge()
        {
            if (null !== $age = $this->headers->get('Age')) {
                return (int)$age;
            }
            return max(time() - $this->getDate()->format('U'), 0);
        }
        public function expire()
        {
            if ($this->isFresh()) {
                $this->headers->set('Age', $this->getMaxAge());
            }
            return $this;
        }
        public function getExpires()
        {
            try {
                return $this->headers->getDate('Expires');
            }
            catch(\RuntimeException $e) {
                return \DateTime::createFromFormat(DATE_RFC2822, 'Sat, 01 Jan 00 00:00:00 +0000');
            }
        }
        public function setExpires(\DateTime $date = null)
        {
            if (null === $date) {
                $this->headers->remove('Expires');
            } else {
                $date = clone $date;
                $date->setTimezone(new \DateTimeZone('UTC'));
                $this->headers->set('Expires', $date->format('D, d M Y H:i:s') . ' GMT');
            }
            return $this;
        }
        public function getMaxAge()
        {
            if ($this->headers->hasCacheControlDirective('s-maxage')) {
                return (int)$this->headers->getCacheControlDirective('s-maxage');
            }
            if ($this->headers->hasCacheControlDirective('max-age')) {
                return (int)$this->headers->getCacheControlDirective('max-age');
            }
            if (null !== $this->getExpires()) {
                return $this->getExpires()->format('U') - $this->getDate()->format('U');
            }
            return null;
        }
        public function setMaxAge($value)
        {
            $this->headers->addCacheControlDirective('max-age', $value);
            return $this;
        }
        public function setSharedMaxAge($value)
        {
            $this->setPublic();
            $this->headers->addCacheControlDirective('s-maxage', $value);
            return $this;
        }
        public function getTtl()
        {
            if (null !== $maxAge = $this->getMaxAge()) {
                return $maxAge - $this->getAge();
            }
            return null;
        }
        public function setTtl($seconds)
        {
            $this->setSharedMaxAge($this->getAge() + $seconds);
            return $this;
        }
        public function setClientTtl($seconds)
        {
            $this->setMaxAge($this->getAge() + $seconds);
            return $this;
        }
        public function getLastModified()
        {
            return $this->headers->getDate('Last-Modified');
        }
        public function setLastModified(\DateTime $date = null)
        {
            if (null === $date) {
                $this->headers->remove('Last-Modified');
            } else {
                $date = clone $date;
                $date->setTimezone(new \DateTimeZone('UTC'));
                $this->headers->set('Last-Modified', $date->format('D, d M Y H:i:s') . ' GMT');
            }
            return $this;
        }
        public function getEtag()
        {
            return $this->headers->get('ETag');
        }
        public function setEtag($etag = null, $weak = false)
        {
            if (null === $etag) {
                $this->headers->remove('Etag');
            } else {
                if (0 !== strpos($etag, '"')) {
                    $etag = '"' . $etag . '"';
                }
                $this->headers->set('ETag', (true === $weak ? 'W/' : '') . $etag);
            }
            return $this;
        }
        public function setCache(array $options)
        {
            if ($diff = array_diff(array_keys($options), array('etag', 'last_modified', 'max_age', 's_maxage', 'private', 'public'))) {
                throw new \InvalidArgumentException(sprintf('Response does not support the following options: "%s".', implode('", "', array_values($diff))));
            }
            if (isset($options['etag'])) {
                $this->setEtag($options['etag']);
            }
            if (isset($options['last_modified'])) {
                $this->setLastModified($options['last_modified']);
            }
            if (isset($options['max_age'])) {
                $this->setMaxAge($options['max_age']);
            }
            if (isset($options['s_maxage'])) {
                $this->setSharedMaxAge($options['s_maxage']);
            }
            if (isset($options['public'])) {
                if ($options['public']) {
                    $this->setPublic();
                } else {
                    $this->setPrivate();
                }
            }
            if (isset($options['private'])) {
                if ($options['private']) {
                    $this->setPrivate();
                } else {
                    $this->setPublic();
                }
            }
            return $this;
        }
        public function setNotModified()
        {
            $this->setStatusCode(304);
            $this->setContent(null);
            foreach (array('Allow', 'Content-Encoding', 'Content-Language', 'Content-Length', 'Content-MD5', 'Content-Type', 'Last-Modified') as $header) {
                $this->headers->remove($header);
            }
            return $this;
        }
        public function hasVary()
        {
            return null !== $this->headers->get('Vary');
        }
        public function getVary()
        {
            if (!$vary = $this->headers->get('Vary')) {
                return array();
            }
            return is_array($vary) ? $vary : preg_split('/[\s,]+/', $vary);
        }
        public function setVary($headers, $replace = true)
        {
            $this->headers->set('Vary', $headers, $replace);
            return $this;
        }
        public function isNotModified(Request $request)
        {
            if (!$request->isMethodSafe()) {
                return false;
            }
            $lastModified = $request->headers->get('If-Modified-Since');
            $notModified = false;
            if ($etags = $request->getEtags()) {
                $notModified = (in_array($this->getEtag(), $etags) || in_array('*', $etags)) && (!$lastModified || $this->headers->get('Last-Modified') == $lastModified);
            } elseif ($lastModified) {
                $notModified = $lastModified == $this->headers->get('Last-Modified');
            }
            if ($notModified) {
                $this->setNotModified();
            }
            return $notModified;
        }
        public function isInvalid()
        {
            return $this->statusCode < 100 || $this->statusCode >= 600;
        }
        public function isInformational()
        {
            return $this->statusCode >= 100 && $this->statusCode < 200;
        }
        public function isSuccessful()
        {
            return $this->statusCode >= 200 && $this->statusCode < 300;
        }
        public function isRedirection()
        {
            return $this->statusCode >= 300 && $this->statusCode < 400;
        }
        public function isClientError()
        {
            return $this->statusCode >= 400 && $this->statusCode < 500;
        }
        public function isServerError()
        {
            return $this->statusCode >= 500 && $this->statusCode < 600;
        }
        public function isOk()
        {
            return 200 === $this->statusCode;
        }
        public function isForbidden()
        {
            return 403 === $this->statusCode;
        }
        public function isNotFound()
        {
            return 404 === $this->statusCode;
        }
        public function isRedirect($location = null)
        {
            return in_array($this->statusCode, array(201, 301, 302, 303, 307, 308)) && (null === $location ? : $location == $this->headers->get('Location'));
        }
        public function isEmpty()
        {
            return in_array($this->statusCode, array(201, 204, 304));
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\ResponseHeaderBag.php
     */
    class ResponseHeaderBag extends HeaderBag
    {
        const COOKIES_FLAT = 'flat';
        const COOKIES_ARRAY = 'array';
        const DISPOSITION_ATTACHMENT = 'attachment';
        const DISPOSITION_INLINE = 'inline';
        protected $computedCacheControl = array();
        protected $cookies = array();
        protected $headerNames = array();
        public function __construct(array $headers = array())
        {
            parent::__construct($headers);
            if (!isset($this->headers['cache-control'])) {
                $this->set('Cache-Control', '');
            }
        }
        public function __toString()
        {
            $cookies = '';
            foreach ($this->getCookies() as $cookie) {
                $cookies.= 'Set-Cookie: ' . $cookie . "\r\n";
            }
            ksort($this->headerNames);
            return parent::__toString() . $cookies;
        }
        public function allPreserveCase()
        {
            return array_combine($this->headerNames, $this->headers);
        }
        public function replace(array $headers = array())
        {
            $this->headerNames = array();
            parent::replace($headers);
            if (!isset($this->headers['cache-control'])) {
                $this->set('Cache-Control', '');
            }
        }
        public function set($key, $values, $replace = true)
        {
            parent::set($key, $values, $replace);
            $uniqueKey = strtr(strtolower($key), '_', '-');
            $this->headerNames[$uniqueKey] = $key;
            if (in_array($uniqueKey, array('cache-control', 'etag', 'last-modified', 'expires'))) {
                $computed = $this->computeCacheControlValue();
                $this->headers['cache-control'] = array($computed);
                $this->headerNames['cache-control'] = 'Cache-Control';
                $this->computedCacheControl = $this->parseCacheControl($computed);
            }
        }
        public function remove($key)
        {
            parent::remove($key);
            $uniqueKey = strtr(strtolower($key), '_', '-');
            unset($this->headerNames[$uniqueKey]);
            if ('cache-control' === $uniqueKey) {
                $this->computedCacheControl = array();
            }
        }
        public function hasCacheControlDirective($key)
        {
            return array_key_exists($key, $this->computedCacheControl);
        }
        public function getCacheControlDirective($key)
        {
            return array_key_exists($key, $this->computedCacheControl) ? $this->computedCacheControl[$key] : null;
        }
        public function setCookie(Cookie $cookie)
        {
            $this->cookies[$cookie->getDomain() ][$cookie->getPath() ][$cookie->getName() ] = $cookie;
        }
        public function removeCookie($name, $path = '/', $domain = null)
        {
            if (null === $path) {
                $path = '/';
            }
            unset($this->cookies[$domain][$path][$name]);
            if (empty($this->cookies[$domain][$path])) {
                unset($this->cookies[$domain][$path]);
                if (empty($this->cookies[$domain])) {
                    unset($this->cookies[$domain]);
                }
            }
        }
        public function getCookies($format = self::COOKIES_FLAT)
        {
            if (!in_array($format, array(self::COOKIES_FLAT, self::COOKIES_ARRAY))) {
                throw new \InvalidArgumentException(sprintf('Format "%s" invalid (%s).', $format, implode(', ', array(self::COOKIES_FLAT, self::COOKIES_ARRAY))));
            }
            if (self::COOKIES_ARRAY === $format) {
                return $this->cookies;
            }
            $flattenedCookies = array();
            foreach ($this->cookies as $path) {
                foreach ($path as $cookies) {
                    foreach ($cookies as $cookie) {
                        $flattenedCookies[] = $cookie;
                    }
                }
            }
            return $flattenedCookies;
        }
        public function clearCookie($name, $path = '/', $domain = null)
        {
            $this->setCookie(new Cookie($name, null, 1, $path, $domain));
        }
        public function makeDisposition($disposition, $filename, $filenameFallback = '')
        {
            if (!in_array($disposition, array(self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE))) {
                throw new \InvalidArgumentException(sprintf('The disposition must be either "%s" or "%s".', self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE));
            }
            if ('' == $filenameFallback) {
                $filenameFallback = $filename;
            }
            if (!preg_match('/^[\x20-\x7e]*$/', $filenameFallback)) {
                throw new \InvalidArgumentException('The filename fallback must only contain ASCII characters.');
            }
            if (false !== strpos($filenameFallback, '%')) {
                throw new \InvalidArgumentException('The filename fallback cannot contain the "%" character.');
            }
            if (false !== strpos($filename, '/') || false !== strpos($filename, '\\') || false !== strpos($filenameFallback, '/') || false !== strpos($filenameFallback, '\\')) {
                throw new \InvalidArgumentException('The filename and the fallback cannot contain the "/" and "\\" characters.');
            }
            $output = sprintf('%s; filename="%s"', $disposition, str_replace('"', '\\"', $filenameFallback));
            if ($filename !== $filenameFallback) {
                $output.= sprintf("; filename*=utf-8''%s", rawurlencode($filename));
            }
            return $output;
        }
        protected function computeCacheControlValue()
        {
            if (!$this->cacheControl && !$this->has('ETag') && !$this->has('Last-Modified') && !$this->has('Expires')) {
                return 'no-cache';
            }
            if (!$this->cacheControl) {
                return 'private, must-revalidate';
            }
            $header = $this->getCacheControlHeader();
            if (isset($this->cacheControl['public']) || isset($this->cacheControl['private'])) {
                return $header;
            }
            if (!isset($this->cacheControl['s-maxage'])) {
                return $header . ', private';
            }
            return $header;
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\ServerBag.php
     */
    class ServerBag extends ParameterBag
    {
        public function getHeaders()
        {
            $headers = array();
            foreach ($this->parameters as $key => $value) {
                if (0 === strpos($key, 'HTTP_')) {
                    $headers[substr($key, 5) ] = $value;
                } elseif (in_array($key, array('CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE'))) {
                    $headers[$key] = $value;
                }
            }
            if (isset($this->parameters['PHP_AUTH_USER'])) {
                $headers['PHP_AUTH_USER'] = $this->parameters['PHP_AUTH_USER'];
                $headers['PHP_AUTH_PW'] = isset($this->parameters['PHP_AUTH_PW']) ? $this->parameters['PHP_AUTH_PW'] : '';
            } else {
                $authorizationHeader = null;
                if (isset($this->parameters['HTTP_AUTHORIZATION'])) {
                    $authorizationHeader = $this->parameters['HTTP_AUTHORIZATION'];
                } elseif (isset($this->parameters['REDIRECT_HTTP_AUTHORIZATION'])) {
                    $authorizationHeader = $this->parameters['REDIRECT_HTTP_AUTHORIZATION'];
                }
                if ((null !== $authorizationHeader) && (0 === stripos($authorizationHeader, 'basic'))) {
                    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)));
                    if (count($exploded) == 2) {
                        list($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']) = $exploded;
                    }
                }
            }
            if (isset($headers['PHP_AUTH_USER'])) {
                $headers['AUTHORIZATION'] = 'Basic ' . base64_encode($headers['PHP_AUTH_USER'] . ':' . $headers['PHP_AUTH_PW']);
            }
            return $headers;
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\StreamedResponse.php
     */
    class StreamedResponse extends Response
    {
        protected $callback;
        protected $streamed;
        public function __construct($callback = null, $status = 200, $headers = array())
        {
            parent::__construct(null, $status, $headers);
            if (null !== $callback) {
                $this->setCallback($callback);
            }
            $this->streamed = false;
        }
        public static function create($callback = null, $status = 200, $headers = array())
        {
            return new static ($callback, $status, $headers);
        }
        public function setCallback($callback)
        {
            if (!is_callable($callback)) {
                throw new \LogicException('The Response callback must be a valid PHP callable.');
            }
            $this->callback = $callback;
        }
        public function prepare(Request $request)
        {
            $this->headers->set('Cache-Control', 'no-cache');
            return parent::prepare($request);
        }
        public function sendContent()
        {
            if ($this->streamed) {
                return;
            }
            $this->streamed = true;
            if (null === $this->callback) {
                throw new \LogicException('The Response callback must not be null.');
            }
            call_user_func($this->callback);
        }
        public function setContent($content)
        {
            if (null !== $content) {
                throw new \LogicException('The content cannot be set on a StreamedResponse instance.');
            }
        }
        public function getContent()
        {
            return false;
        }
    }
}
namespace Symfony\Component\HttpFoundation\Session\Flash {
    use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface.php
     */
    interface FlashBagInterface extends SessionBagInterface {
        public function add($type, $message);
        public function set($type, $message);
        public function peek($type, array $default = array());
        public function peekAll();
        public function get($type, array $default = array());
        public function all();
        public function setAll(array $messages);
        public function has($type);
        public function keys();
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag.php
     */
    class AutoExpireFlashBag implements FlashBagInterface
    {
        private $name = 'flashes';
        private $flashes = array();
        private $storageKey;
        public function __construct($storageKey = '_sf2_flashes')
        {
            $this->storageKey = $storageKey;
            $this->flashes = array('display' => array(), 'new' => array());
        }
        public function getName()
        {
            return $this->name;
        }
        public function setName($name)
        {
            $this->name = $name;
        }
        public function initialize(array & $flashes)
        {
            $this->flashes = & $flashes;
            $this->flashes['display'] = array_key_exists('new', $this->flashes) ? $this->flashes['new'] : array();
            $this->flashes['new'] = array();
        }
        public function add($type, $message)
        {
            $this->flashes['new'][$type][] = $message;
        }
        public function peek($type, array $default = array())
        {
            return $this->has($type) ? $this->flashes['display'][$type] : $default;
        }
        public function peekAll()
        {
            return array_key_exists('display', $this->flashes) ? (array)$this->flashes['display'] : array();
        }
        public function get($type, array $default = array())
        {
            $return = $default;
            if (!$this->has($type)) {
                return $return;
            }
            if (isset($this->flashes['display'][$type])) {
                $return = $this->flashes['display'][$type];
                unset($this->flashes['display'][$type]);
            }
            return $return;
        }
        public function all()
        {
            $return = $this->flashes['display'];
            $this->flashes = array('new' => array(), 'display' => array());
            return $return;
        }
        public function setAll(array $messages)
        {
            $this->flashes['new'] = $messages;
        }
        public function set($type, $messages)
        {
            $this->flashes['new'][$type] = (array)$messages;
        }
        public function has($type)
        {
            return array_key_exists($type, $this->flashes['display']) && $this->flashes['display'][$type];
        }
        public function keys()
        {
            return array_keys($this->flashes['display']);
        }
        public function getStorageKey()
        {
            return $this->storageKey;
        }
        public function clear()
        {
            return $this->all();
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Flash\FlashBag.php
     */
    class FlashBag implements FlashBagInterface, \IteratorAggregate, \Countable
    {
        private $name = 'flashes';
        private $flashes = array();
        private $storageKey;
        public function __construct($storageKey = '_sf2_flashes')
        {
            $this->storageKey = $storageKey;
        }
        public function getName()
        {
            return $this->name;
        }
        public function setName($name)
        {
            $this->name = $name;
        }
        public function initialize(array & $flashes)
        {
            $this->flashes = & $flashes;
        }
        public function add($type, $message)
        {
            $this->flashes[$type][] = $message;
        }
        public function peek($type, array $default = array())
        {
            return $this->has($type) ? $this->flashes[$type] : $default;
        }
        public function peekAll()
        {
            return $this->flashes;
        }
        public function get($type, array $default = array())
        {
            if (!$this->has($type)) {
                return $default;
            }
            $return = $this->flashes[$type];
            unset($this->flashes[$type]);
            return $return;
        }
        public function all()
        {
            $return = $this->peekAll();
            $this->flashes = array();
            return $return;
        }
        public function set($type, $messages)
        {
            $this->flashes[$type] = (array)$messages;
        }
        public function setAll(array $messages)
        {
            $this->flashes = $messages;
        }
        public function has($type)
        {
            return array_key_exists($type, $this->flashes) && $this->flashes[$type];
        }
        public function keys()
        {
            return array_keys($this->flashes);
        }
        public function getStorageKey()
        {
            return $this->storageKey;
        }
        public function clear()
        {
            return $this->all();
        }
        public function getIterator()
        {
            return new \ArrayIterator($this->all());
        }
        public function count()
        {
            trigger_error(sprintf('%s() is deprecated since 2.2 and will be removed in 2.3', __METHOD__), E_USER_DEPRECATED);
            return count($this->flashes);
        }
    }
}
namespace Symfony\Component\HttpFoundation\Session\Storage\Handler
{
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler.php
     */
    if (version_compare(phpversion(), '5.4.0', '>=')) {
        class NativeSessionHandler extends \SessionHandler
        {
        }
    } else {
        class NativeSessionHandler
        {
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler.php
     */
    class NullSessionHandler implements \SessionHandlerInterface
    {
        public function open($savePath, $sessionName)
        {
            return true;
        }
        public function close()
        {
            return true;
        }
        public function read($sessionId)
        {
            return '';
        }
        public function write($sessionId, $data)
        {
            return true;
        }
        public function destroy($sessionId)
        {
            return true;
        }
        public function gc($lifetime)
        {
            return true;
        }
    }
}
namespace Symfony\Component\HttpFoundation\Session\Storage\Proxy
{
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Proxy\AbstractProxy.php
     */
    abstract class AbstractProxy
    {
        protected $wrapper = false;
        protected $active = false;
        protected $saveHandlerName;
        public function getSaveHandlerName()
        {
            return $this->saveHandlerName;
        }
        public function isSessionHandlerInterface()
        {
            return ($this instanceof \SessionHandlerInterface);
        }
        public function isWrapper()
        {
            return $this->wrapper;
        }
        public function isActive()
        {
            return $this->active;
        }
        public function setActive($flag)
        {
            $this->active = (bool)$flag;
        }
        public function getId()
        {
            return session_id();
        }
        public function setId($id)
        {
            if ($this->isActive()) {
                throw new \LogicException('Cannot change the ID of an active session');
            }
            session_id($id);
        }
        public function getName()
        {
            return session_name();
        }
        public function setName($name)
        {
            if ($this->isActive()) {
                throw new \LogicException('Cannot change the name of an active session');
            }
            session_name($name);
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy.php
     */
    class NativeProxy extends AbstractProxy
    {
        public function __construct()
        {
            $this->saveHandlerName = ini_get('session.save_handler');
        }
        public function isWrapper()
        {
            return false;
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy.php
     */
    class SessionHandlerProxy extends AbstractProxy implements \SessionHandlerInterface
    {
        protected $handler;
        public function __construct(\SessionHandlerInterface $handler)
        {
            $this->handler = $handler;
            $this->wrapper = ($handler instanceof \SessionHandler);
            $this->saveHandlerName = $this->wrapper ? ini_get('session.save_handler') : 'user';
        }
        public function open($savePath, $sessionName)
        {
            $return = (bool)$this->handler->open($savePath, $sessionName);
            if (true === $return) {
                $this->active = true;
            }
            return $return;
        }
        public function close()
        {
            $this->active = false;
            return (bool)$this->handler->close();
        }
        public function read($id)
        {
            return (string)$this->handler->read($id);
        }
        public function write($id, $data)
        {
            return (bool)$this->handler->write($id, $data);
        }
        public function destroy($id)
        {
            return (bool)$this->handler->destroy($id);
        }
        public function gc($maxlifetime)
        {
            return (bool)$this->handler->gc($maxlifetime);
        }
    }
}
namespace Symfony\Component\HttpFoundation\Session\Storage
{
    use Symfony\Component\HttpFoundation\Session\Storage\Proxy\AbstractProxy;
    use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
    use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
    use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;
    use Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy;
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface.php
     */
    interface SessionStorageInterface
    {
        public function start();
        public function isStarted();
        public function getId();
        public function setId($id);
        public function getName();
        public function setName($name);
        public function regenerate($destroy = false, $lifetime = null);
        public function save();
        public function clear();
        public function getBag($name);
        public function registerBag(SessionBagInterface $bag);
        public function getMetadataBag();
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\MetadataBag.php
     */
    class MetadataBag implements SessionBagInterface
    {
        const CREATED = 'c';
        const UPDATED = 'u';
        const LIFETIME = 'l';
        private $name = '__metadata';
        private $storageKey;
        protected $meta = array();
        private $lastUsed;
        public function __construct($storageKey = '_sf2_meta')
        {
            $this->storageKey = $storageKey;
            $this->meta = array(self::CREATED => 0, self::UPDATED => 0, self::LIFETIME => 0);
        }
        public function initialize(array & $array)
        {
            $this->meta = & $array;
            if (isset($array[self::CREATED])) {
                $this->lastUsed = $this->meta[self::UPDATED];
                $this->meta[self::UPDATED] = time();
            } else {
                $this->stampCreated();
            }
        }
        public function getLifetime()
        {
            return $this->meta[self::LIFETIME];
        }
        public function stampNew($lifetime = null)
        {
            $this->stampCreated($lifetime);
        }
        public function getStorageKey()
        {
            return $this->storageKey;
        }
        public function getCreated()
        {
            return $this->meta[self::CREATED];
        }
        public function getLastUsed()
        {
            return $this->lastUsed;
        }
        public function clear()
        {
        }
        public function getName()
        {
            return $this->name;
        }
        public function setName($name)
        {
            $this->name = $name;
        }
        private function stampCreated($lifetime = null)
        {
            $timeStamp = time();
            $this->meta[self::CREATED] = $this->meta[self::UPDATED] = $this->lastUsed = $timeStamp;
            $this->meta[self::LIFETIME] = (null === $lifetime) ? ini_get('session.cookie_lifetime') : $lifetime;
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage.php
     */
    class MockArraySessionStorage implements SessionStorageInterface
    {
        protected $id = '';
        protected $name;
        protected $started = false;
        protected $closed = false;
        protected $data = array();
        protected $metadataBag;
        protected $bags;
        public function __construct($name = 'MOCKSESSID', MetadataBag $metaBag = null)
        {
            $this->name = $name;
            $this->setMetadataBag($metaBag);
        }
        public function setSessionData(array $array)
        {
            $this->data = $array;
        }
        public function start()
        {
            if ($this->started && !$this->closed) {
                return true;
            }
            if (empty($this->id)) {
                $this->id = $this->generateId();
            }
            $this->loadSession();
            return true;
        }
        public function regenerate($destroy = false, $lifetime = null)
        {
            if (!$this->started) {
                $this->start();
            }
            $this->metadataBag->stampNew($lifetime);
            $this->id = $this->generateId();
            return true;
        }
        public function getId()
        {
            return $this->id;
        }
        public function setId($id)
        {
            if ($this->started) {
                throw new \LogicException('Cannot set session ID after the session has started.');
            }
            $this->id = $id;
        }
        public function getName()
        {
            return $this->name;
        }
        public function setName($name)
        {
            $this->name = $name;
        }
        public function save()
        {
            if (!$this->started || $this->closed) {
                throw new \RuntimeException("Trying to save a session that was not started yet or was already closed");
            }
            $this->closed = false;
        }
        public function clear()
        {
            foreach ($this->bags as $bag) {
                $bag->clear();
            }
            $this->data = array();
            $this->loadSession();
        }
        public function registerBag(SessionBagInterface $bag)
        {
            $this->bags[$bag->getName() ] = $bag;
        }
        public function getBag($name)
        {
            if (!isset($this->bags[$name])) {
                throw new \InvalidArgumentException(sprintf('The SessionBagInterface %s is not registered.', $name));
            }
            if (!$this->started) {
                $this->start();
            }
            return $this->bags[$name];
        }
        public function isStarted()
        {
            return $this->started;
        }
        public function setMetadataBag(MetadataBag $bag = null)
        {
            if (null === $bag) {
                $bag = new MetadataBag();
            }
            $this->metadataBag = $bag;
        }
        public function getMetadataBag()
        {
            return $this->metadataBag;
        }
        protected function generateId()
        {
            return sha1(uniqid(mt_rand()));
        }
        protected function loadSession()
        {
            $bags = array_merge($this->bags, array($this->metadataBag));
            foreach ($bags as $bag) {
                $key = $bag->getStorageKey();
                $this->data[$key] = isset($this->data[$key]) ? $this->data[$key] : array();
                $bag->initialize($this->data[$key]);
            }
            $this->started = true;
            $this->closed = false;
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage.php
     */
    class MockFileSessionStorage extends MockArraySessionStorage
    {
        private $savePath;
        private $sessionData;
        public function __construct($savePath = null, $name = 'MOCKSESSID', MetadataBag $metaBag = null)
        {
            if (null === $savePath) {
                $savePath = sys_get_temp_dir();
            }
            if (!is_dir($savePath)) {
                mkdir($savePath, 0777, true);
            }
            $this->savePath = $savePath;
            parent::__construct($name, $metaBag);
        }
        public function start()
        {
            if ($this->started) {
                return true;
            }
            if (!$this->id) {
                $this->id = $this->generateId();
            }
            $this->read();
            $this->started = true;
            return true;
        }
        public function regenerate($destroy = false, $lifetime = null)
        {
            if (!$this->started) {
                $this->start();
            }
            if ($destroy) {
                $this->destroy();
            }
            return parent::regenerate($destroy, $lifetime);
        }
        public function save()
        {
            if (!$this->started) {
                throw new \RuntimeException("Trying to save a session that was not started yet or was already closed");
            }
            file_put_contents($this->getFilePath(), serialize($this->data));
            $this->started = false;
        }
        private function destroy()
        {
            if (is_file($this->getFilePath())) {
                unlink($this->getFilePath());
            }
        }
        private function getFilePath()
        {
            return $this->savePath . '/' . $this->id . '.mocksess';
        }
        private function read()
        {
            $filePath = $this->getFilePath();
            $this->data = is_readable($filePath) && is_file($filePath) ? unserialize(file_get_contents($filePath)) : array();
            $this->loadSession();
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage.php
     */
    class NativeSessionStorage implements SessionStorageInterface
    {
        protected $bags;
        protected $started = false;
        protected $closed = false;
        protected $saveHandler;
        protected $metadataBag;
        public function __construct(array $options = array(), $handler = null, MetadataBag $metaBag = null)
        {
            ini_set('session.cache_limiter', '');
            ini_set('session.use_cookies', 1);
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                session_register_shutdown();
            } else {
                register_shutdown_function('session_write_close');
            }
            $this->setMetadataBag($metaBag);
            $this->setOptions($options);
            $this->setSaveHandler($handler);
        }
        public function getSaveHandler()
        {
            return $this->saveHandler;
        }
        public function start()
        {
            if ($this->started && !$this->closed) {
                return true;
            }
            if (!$this->started && !$this->closed && $this->saveHandler->isActive() && $this->saveHandler->isSessionHandlerInterface()) {
                $this->loadSession();
                return true;
            }
            if (ini_get('session.use_cookies') && headers_sent()) {
                throw new \RuntimeException('Failed to start the session because headers have already been sent.');
            }
            if (!session_start()) {
                throw new \RuntimeException('Failed to start the session');
            }
            $this->loadSession();
            if (!$this->saveHandler->isWrapper() && !$this->saveHandler->isSessionHandlerInterface()) {
                $this->saveHandler->setActive(false);
            }
            return true;
        }
        public function getId()
        {
            if (!$this->started) {
                return '';
            }
            return $this->saveHandler->getId();
        }
        public function setId($id)
        {
            $this->saveHandler->setId($id);
        }
        public function getName()
        {
            return $this->saveHandler->getName();
        }
        public function setName($name)
        {
            $this->saveHandler->setName($name);
        }
        public function regenerate($destroy = false, $lifetime = null)
        {
            if (null !== $lifetime) {
                ini_set('session.cookie_lifetime', $lifetime);
            }
            if ($destroy) {
                $this->metadataBag->stampNew();
            }
            return session_regenerate_id($destroy);
        }
        public function save()
        {
            session_write_close();
            if (!$this->saveHandler->isWrapper() && !$this->getSaveHandler()->isSessionHandlerInterface()) {
                $this->saveHandler->setActive(false);
            }
            $this->closed = true;
        }
        public function clear()
        {
            foreach ($this->bags as $bag) {
                $bag->clear();
            }
            $_SESSION = array();
            $this->loadSession();
        }
        public function registerBag(SessionBagInterface $bag)
        {
            $this->bags[$bag->getName() ] = $bag;
        }
        public function getBag($name)
        {
            if (!isset($this->bags[$name])) {
                throw new \InvalidArgumentException(sprintf('The SessionBagInterface %s is not registered.', $name));
            }
            if ($this->saveHandler->isActive() && !$this->started) {
                $this->loadSession();
            } elseif (!$this->started) {
                $this->start();
            }
            return $this->bags[$name];
        }
        public function setMetadataBag(MetadataBag $metaBag = null)
        {
            if (null === $metaBag) {
                $metaBag = new MetadataBag();
            }
            $this->metadataBag = $metaBag;
        }
        public function getMetadataBag()
        {
            return $this->metadataBag;
        }
        public function isStarted()
        {
            return $this->started;
        }
        public function setOptions(array $options)
        {
            $validOptions = array_flip(array('cache_limiter', 'cookie_domain', 'cookie_httponly', 'cookie_lifetime', 'cookie_path', 'cookie_secure', 'entropy_file', 'entropy_length', 'gc_divisor', 'gc_maxlifetime', 'gc_probability', 'hash_bits_per_character', 'hash_function', 'name', 'referer_check', 'serialize_handler', 'use_cookies', 'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled', 'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name', 'upload_progress.freq', 'upload_progress.min-freq', 'url_rewriter.tags',));
            foreach ($options as $key => $value) {
                if (isset($validOptions[$key])) {
                    ini_set('session.' . $key, $value);
                }
            }
        }
        public function setSaveHandler($saveHandler = null)
        {
            if (!$saveHandler instanceof AbstractProxy && $saveHandler instanceof \SessionHandlerInterface) {
                $saveHandler = new SessionHandlerProxy($saveHandler);
            } elseif (!$saveHandler instanceof AbstractProxy) {
                $saveHandler = new NativeProxy();
            }
            $this->saveHandler = $saveHandler;
            if ($this->saveHandler instanceof \SessionHandlerInterface) {
                if (version_compare(phpversion(), '5.4.0', '>=')) {
                    session_set_save_handler($this->saveHandler, false);
                } else {
                    session_set_save_handler(array($this->saveHandler, 'open'), array($this->saveHandler, 'close'), array($this->saveHandler, 'read'), array($this->saveHandler, 'write'), array($this->saveHandler, 'destroy'), array($this->saveHandler, 'gc'));
                }
            }
        }
        protected function loadSession(array & $session = null)
        {
            if (null === $session) {
                $session = & $_SESSION;
            }
            $bags = array_merge($this->bags, array($this->metadataBag));
            foreach ($bags as $bag) {
                $key = $bag->getStorageKey();
                $session[$key] = isset($session[$key]) ? $session[$key] : array();
                $bag->initialize($session[$key]);
            }
            $this->started = true;
            $this->closed = false;
        }
    }
}
namespace Symfony\Component\HttpFoundation\Session
{
    use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
    use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
    use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
    use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
    use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
    use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
    use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
    use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\Session.php
     */
    class Session implements SessionInterface, \IteratorAggregate, \Countable
    {
        protected $storage;
        private $flashName;
        private $attributeName;
        public function __construct(SessionStorageInterface $storage = null, AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null)
        {
            $this->storage = $storage ? : new NativeSessionStorage();
            $attributes = $attributes ? : new AttributeBag();
            $this->attributeName = $attributes->getName();
            $this->registerBag($attributes);
            $flashes = $flashes ? : new FlashBag();
            $this->flashName = $flashes->getName();
            $this->registerBag($flashes);
        }
        public function start()
        {
            return $this->storage->start();
        }
        public function has($name)
        {
            return $this->storage->getBag($this->attributeName)->has($name);
        }
        public function get($name, $default = null)
        {
            return $this->storage->getBag($this->attributeName)->get($name, $default);
        }
        public function set($name, $value)
        {
            $this->storage->getBag($this->attributeName)->set($name, $value);
        }
        public function all()
        {
            return $this->storage->getBag($this->attributeName)->all();
        }
        public function replace(array $attributes)
        {
            $this->storage->getBag($this->attributeName)->replace($attributes);
        }
        public function remove($name)
        {
            return $this->storage->getBag($this->attributeName)->remove($name);
        }
        public function clear()
        {
            $this->storage->getBag($this->attributeName)->clear();
        }
        public function isStarted()
        {
            return $this->storage->isStarted();
        }
        public function getIterator()
        {
            return new \ArrayIterator($this->storage->getBag($this->attributeName)->all());
        }
        public function count()
        {
            return count($this->storage->getBag($this->attributeName)->all());
        }
        public function invalidate($lifetime = null)
        {
            $this->storage->clear();
            return $this->migrate(true, $lifetime);
        }
        public function migrate($destroy = false, $lifetime = null)
        {
            return $this->storage->regenerate($destroy, $lifetime);
        }
        public function save()
        {
            $this->storage->save();
        }
        public function getId()
        {
            return $this->storage->getId();
        }
        public function setId($id)
        {
            $this->storage->setId($id);
        }
        public function getName()
        {
            return $this->storage->getName();
        }
        public function setName($name)
        {
            $this->storage->setName($name);
        }
        public function getMetadataBag()
        {
            return $this->storage->getMetadataBag();
        }
        public function registerBag(SessionBagInterface $bag)
        {
            $this->storage->registerBag($bag);
        }
        public function getBag($name)
        {
            return $this->storage->getBag($name);
        }
        public function getFlashBag()
        {
            return $this->getBag($this->flashName);
        }
        public function getFlashes()
        {
            $all = $this->getBag($this->flashName)->all();
            $return = array();
            if ($all) {
                foreach ($all as $name => $array) {
                    if (is_numeric(key($array))) {
                        $return[$name] = reset($array);
                    } else {
                        $return[$name] = $array;
                    }
                }
            }
            return $return;
        }
        public function setFlashes($values)
        {
            foreach ($values as $name => $value) {
                $this->getBag($this->flashName)->set($name, $value);
            }
        }
        public function getFlash($name, $default = null)
        {
            $return = $this->getBag($this->flashName)->get($name);
            return empty($return) ? $default : reset($return);
        }
        public function setFlash($name, $value)
        {
            $this->getBag($this->flashName)->set($name, $value);
        }
        public function hasFlash($name)
        {
            return $this->getBag($this->flashName)->has($name);
        }
        public function removeFlash($name)
        {
            $this->getBag($this->flashName)->get($name);
        }
        public function clearFlashes()
        {
            return $this->getBag($this->flashName)->clear();
        }
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\SessionBagInterface.php
     */
    interface SessionBagInterface
    {
        public function getName();
        public function initialize(array & $array);
        public function getStorageKey();
        public function clear();
    }
    /**
     * laravel\vendor\Symfony\Component\HttpFoundation\Session\SessionInterface.php
     */
    interface SessionInterface
    {
        public function start();
        public function getId();
        public function setId($id);
        public function getName();
        public function setName($name);
        public function invalidate($lifetime = null);
        public function migrate($destroy = false, $lifetime = null);
        public function save();
        public function has($name);
        public function get($name, $default = null);
        public function set($name, $value);
        public function all();
        public function replace(array $attributes);
        public function remove($name);
        public function clear();
        public function isStarted();
        public function registerBag(SessionBagInterface $bag);
        public function getBag($name);
        public function getMetadataBag();
    }
}
