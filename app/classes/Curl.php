<?php

use PHPHtmlParser\CurlInterface as CurlInterface;
use PHPHtmlParser\Exceptions\CurlException;

class Curl implements CurlInterface {

    private $content;
    private $size;

	/**
	 * A simple curl implementation to get the content of the url.
	 *
	 * @param string $url
	 * @return string
	 * @throws CurlException
	 */
	public function get($url)
	{
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);

        // Necessary for Sainsbury's website
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");

        // Set the Cookie options -- Important for the web
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($ch, CURLOPT_HEADER, true);

        $content = curl_exec($ch);
        if ($content === false)
        {
            // There was a problem
            $error = curl_error($ch);
            throw new CurlException('Error retrieving "'.$url.'" ('.$error.')');
        }

        // Get resource property (like download size etc.)
        $download_size = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);

        // Close curl
        curl_close($ch);

        // Set the download size of this request to $this->size
        $this->size = $download_size;

        return $content;
	}

    /**
    * Return size in bytes or kilobytes
    */
    public function getSize($unit = 'kb')
    {
        if ($unit == 'kb') {
            return round($this->size/1024, 2) . 'kb';
        }

        return $this->size;
    }

    public function getContent()
    {
        return $this->content;
    }
}
