<?php

  class CibulClientSDK
  {
    protected $defaults = array(
      'apiUrl' => 'https://api.cibul.net',
      'version' => 'v1',
      'lang' => 'en'
    );

    public function __construct($key, $options = array())
    {
      if (strlen($key) != 32) throw new Exception('key is invalid');

      $this->key = $key;

      $this->options = array_merge($this->defaults, $options);
    }


    /**
     * set the api key
     *
     * @param  string  the key. should be 32 characters long
     */

    public function setKey($key)
    {
      if (strlen($key) != 32) throw new Exception('key is invalid');

      $this->key = $key;
    }


    /**
     * retrieve event uid from event slug
     *
     * @param  string  $slug  the slug of the event
     *
     * @return   string       the uid of the event
     */

    public function getEventUidFromSlug($slug)
    {
      $data = json_decode($this->curlGet("{$this->options['apiUrl']}/{$this->options['version']}/events/uid/$slug?key={$this->key}"), true);

      if ($data['success']) return $data['data']['uid'];
      
      return false;
    }


    /**
     * retrieve event uid from cibul event link
     *
     * @param string $link - cibul.net event link
     * 
     * @return string      - the uid of the event
     */

    public function getEventUidFromLink($link)
    {
      if (strlen($slug = $this->extractEventSlug($link)))
      {
        return $this->getEventUidFromSlug($slug);
      }

      return false;
    }


    /**
     * get event data from a list of uids
     *
     * @param  array      $uids - uids of events to be retrieved
     * @param  string(2)  $lang - optional - preferred language of retrieved event data
     *
     * @return  array    list of event data sets
     */

    public function getEvents($uids, $lang = false)
    {
      if (!$lang) $lang = $this->options['lang'];

      $url = "{$this->options['apiUrl']}/{$this->options['version']}/events?key={$this->key}&lang=$lang";

      foreach ($uids as $uid)
      {
        $url .= '&uids[]=' . $uid;
      }

      $response = json_decode($this->curlGet($url), true);

      if (!$response['success']) return false;

      return $response['data'];
    }


    /**
     * extract slug from cibul event link
     */

    protected function extractEventSlug($link)
    {
      return preg_replace('/\/(.+)/', '', preg_replace('/(http(s|):|)\/\/cibul.net\/event\//', '', $link));
    }

    /**
     * fetch data at $url using curl
     */

    protected function curlGet($url)
    {
      try
      {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        return curl_exec($ch);  
      }
      catch (Exception $e)
      {
        throw new Exceptions('Error with cURL.');
      }
      
    }

  }