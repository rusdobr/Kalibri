<?php

namespace Kalibri\Cache\Driver;

class File implements BaseInterface {

    /**
     * Construct cache driver
     */
    public function __construct(array $config = null){}

    /**
     * Get value from cache engine by string key
     *
     * @param string $key Key to find in cache server
     *
     * @throws \Kalibri\Cache\Exception with FAILED_TO_GET code
     *
     * @return mixed Stored value
     */
    #[\Override]
    public function get($key)
    {
        $path = $this->path($key);

        if(file_exists($path)) {
            [$expiration, $content] = $this->splitContent(file_get_contents($path));

            if($this->isExpired($expiration)) {
                @unlink($path);
                return null;
            }

            return unserialize($content);
        }

        return null;
    }

    /**
     * Store value in a cache engine.
     *
     * @param string $key Key to associate value
     * @param mixed $value Value to store
     * @param int $expire
     *
     * @return null
     */
    #[\Override]
    public function set($key, $value, $expire = 0): void
    {
        $expire = $expire == 0? \Kalibri\Helper\Date::SEC_IN_YEAR: $expire;
        file_put_contents($this->path($key), (time() + $expire)."\n".serialize($value));
    }

    /**
     * Clear the whole storage
     *
     * @return null
     */
    #[\Override]
    public function clear(): void
    {
        foreach(glob(\Kalibri::app()->getLocation().'Data/cache/*') as $file)
        {
            if( is_file($file) )
            {
                unlink($file); // delete file
            }
        }
    }

    /**
     * Remove single from storage by key
     *
     * @param string $key Key to remove
     *
     * @return null
     */
    #[\Override]
    public function remove($key): void
    {
        $path = $this->path($key);

        if(file_exists($path))
        {
            unlink($path);
        }
    }

    private function isExpired($expiration)
    {
        return (int)$expiration < time();
    }

    private function path($key)
    {
        return \Kalibri::tmp($key);
    }

    private function splitContent($content)
    {
        $firstNewLine = strpos((string) $content, "\n");

        return [substr((string) $content, 0, $firstNewLine), substr((string) $content, $firstNewLine+1)];
    }
}