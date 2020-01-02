<?php

namespace CodeZero\StageFront;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class Shield
{
    /**
     * Check if StageFront requires the user to log in.
     *
     * @return bool
     */
    public function requiresLogin()
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
        $ignoredUrls = Config::get('stagefront.ignore_urls', []);
        $ignoredUrls[] = Config::get('stagefront.url');

        foreach ($ignoredUrls as $url) {
            $url = trim($url, '/');

            if (Request::is($url)) {
                return true;
            }
        }

        return false;
    }
}
