<?php

namespace Janfish\Http;

/**
 * Class HttpCacheControl
 */
class CacheControl
{

    const DEFAULT_EXPIRES_TIME = 60;

    /**
     * @param int $expire
     * @param bool $noCache
     */
    public static function handle(int $expire = self::DEFAULT_EXPIRES_TIME, bool $noCache = false)
    {
        (new self())->cache($expire, $noCache);
    }

    /**
     * @param int $expire
     * @param bool $noCache
     */
    public function cache(int $expire = self::DEFAULT_EXPIRES_TIME, bool $noCache = false): void
    {
        $clientModifiedAt = $this->getClientModifiedAt();
        if (!$noCache && $clientModifiedAt && strtotime($clientModifiedAt) + $expire > time()) {
            header("HTTP/1.1 304");
            exit;
        }
        header("Last-Modified: " . $this->makeExpire() . " GMT");
        header("Expires: " . $this->makeExpire($expire) . " GMT");
        header("Cache-Control: max-age=$expire; private");
//        header("Cache-Control: private");
    }

    /**
     * @return mixed|string
     */
    private function getClientModifiedAt()
    {

        return $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '';
    }

    /**
     * @param int $expire seconds of time
     * @return false|string
     */
    private function makeExpire(int $expire = 0)
    {
        return gmdate("D, d M Y H:i:s", time() + $expire);
    }
}