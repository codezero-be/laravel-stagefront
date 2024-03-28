<?php

namespace CodeZero\StageFront;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class Shield
{
    /**
     * Check if StageFront should deny access to the user
     * with a 403 Forbidden HTTP status.
     *
     * @return bool
     */
    public function shouldDenyAccess()
    {
        return $this->isActive()
            && ($this->hasIpWhitelist() && ! $this->clientIpIsWhitelisted() && $this->allowWhitelistedIpsOnly());
    }

    /**
     * Check if StageFront requires the user to log in.
     *
     * @return bool
     */
    public function requiresLogin()
    {
        return $this->isActive()
            && ( ! $this->hasIpWhitelist()
                || ($this->clientIpIsWhitelisted() && $this->whitelistRequiresLogin())
                || ( ! $this->clientIpIsWhitelisted() && ! $this->allowWhitelistedIpsOnly())
            );
    }

    /**
     * Check if StageFront is active.
     * Once a user logs in, the shield is considered inactive.
     *
     * @return bool
     */
    protected function isActive()
    {
        $enabled = Config::get('stagefront.enabled', false);
        $unlocked = Session::get('stagefront.unlocked', false);

        return $enabled && ! $unlocked && ! $this->currentUrlIsIgnored();
    }

    /**
     * Check if the current URL should be ignored.
     *
     * @return bool
     */
    protected function currentUrlIsIgnored()
    {
        $ignoredDomains = Config::get('stagefront.ignore_domains', []);
        $ignoredUrls = Config::get('stagefront.ignore_urls', []);
        $ignoredUrls[] = Config::get('stagefront.url');

        foreach ($ignoredUrls as $url) {
            $url = trim($url, '/');

            if (Request::is($url)) {
                return true;
            }
        }

        foreach ($ignoredDomains as $url) {
            $url = trim($url, '/');

            if (str_contains(Request::url(), $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the client IP is whitelisted.
     *
     * @return bool
     */
    protected function clientIpIsWhitelisted()
    {
        $clientIp = Request::ip();
        $ips = array_map('trim', $this->getIpWhitelist());

        return in_array($clientIp, $ips);
    }

    /**
     * Check if a IP whitelist is configured.
     *
     * @return bool
     */
    protected function hasIpWhitelist()
    {
        return ! empty($this->getIpWhitelist());
    }

    /**
     * Get the IP whitelist from the config file.
     *
     * @return array
     */
    protected function getIpWhitelist()
    {
        $whitelist = Config::get('stagefront.ip_whitelist', []);

        if (is_array($whitelist)) {
            return $whitelist;
        }

        return explode(',', $whitelist);
    }

    /**
     * Get the option to grant access to whitelisted IP's only.
     *
     * @return bool
     */
    protected function allowWhitelistedIpsOnly()
    {
        return Config::get('stagefront.ip_whitelist_only', false);
    }

    /**
     * Get the option to require users with whitelisted IP's to login.
     *
     * @return bool
     */
    public function whitelistRequiresLogin()
    {
        return Config::get('stagefront.ip_whitelist_require_login', false);
    }
}
