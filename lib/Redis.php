<?php
namespace Lib;

class Redis {

    private $_redis;

    public function __construct()
    {
        $redis = new \Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        $this->_redis = $redis;
    }

    public function setPhoneCode($key, $value, $expires_in)
    {
        $this->_redis->set($key, serialize($value));
        $this->_redis->setTimeout($key, $expires_in);
    }

    public function get($key)
    {
        $key_value = $this->_redis->get($key);
        return unserialize($key_value);
    }

    public function hSet($key, $feild, $val)
    {
        return $this->_redis->hSet($key, $feild, $val);
    }

    public function hGet($key, $feild)
    {
        return $this->_redis->hGet($key, $feild);
    }

    public function hmSet($key, $data)
    {
        $data = (array) $data;
        $feildString = '';
        foreach ($data as $k=>$v) {
            $feildString .= $k . ' ' . $v . ',';
        }
        $this->_redis->hMset($key, $feildString);
    }

    /**
     * @example $num>0:自增 $num<0:自减
     */
    public function hInCrby($key, $feild, $num)
    {
        return $this->_redis->hIncrBy($key, $feild, $num);
    }

    public function hKeys($key)
    {
        return $this->_redis->hKeys($key);
    }
}