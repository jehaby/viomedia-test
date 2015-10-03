<?php


namespace Jehaby\Viomedia;


class User
{
    private static $user;
    private $id;
    private $storage;
    public static $testing = false; // hack for testing


    public static function getInstance($id)
    {
        if (! isset(static::$user) || self::$testing ) {
            static::$user = new static($id);
        }
        return static::$user;
    }


    private function __construct($id)
    {
        $this->db = new DB;
        $statement = $this->db->prepare('SELECT id, storage FROM users WHERE id=?');
        $this->db->executeStatement($statement, [$id]);

        if ( ! $data = $statement->fetch(\PDO::FETCH_ASSOC) ) {
            throw new \LogicException("Can't find user with such id");
        }

        $this->storage = unserialize($data['storage']);
        $this->id = $id;
    }


    private function __clone()
    { }


    public static function serialize($data)
    {
        return serialize($data);
    }


    public static function unserialize($str)
    {
        return unserialize($str);
    }


    public function get($keyStr)
    {
        $res = $this->storage;

        if ($keyStr !== '') {
            foreach (explode('\\', $keyStr) as $key) {
                if (!array_key_exists($key, $res)) {
                    throw new \LogicException('There is no record with such key');
                }
                $res = $res[$key];
            }
        }

        return $res;
    }


    public function set($keyStr, $value)
    {
        if ($keyStr === '') {
            $this->storage = $value;
        } else  {
            $this->writeKeyStringToStorage($keyStr, $value);
        }

        $statement = $this->db->prepare("UPDATE users SET storage=? WHERE id={$this->id}");
        return $this->db->executeStatement($statement, [self::serialize($this->storage)]);
    }


    private function writeKeyStringToStorage($keyStr, $value)
    {
        $keys = explode('\\', $keyStr);
        $lastKey = array_pop($keys);

        $currentValue = &$this->storage;

        foreach ($keys as $currentKey) {
            if (! isset($this->storage[$currentKey])) {
                $currentValue[$currentKey] = [];
            }
            $currentValue = &$currentValue[$currentKey];
        }

        $currentValue[$lastKey] = $value;
    }

}