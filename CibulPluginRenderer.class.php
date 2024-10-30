<?php

  class CibulPluginRenderer
  {
    public function __construct($cibulClient, $cache, $view, $preferredLang = 'en')
    {
      $this->cibulClient = $cibulClient;
      $this->cache = $cache;
      $this->view = $view;
      $this->lang = $preferredLang;
    }

    /**
     * shoves a style tag in the content with all the custom style
     */

    public function renderStyle($content = '')
    {
      return '<style type="text/css">' . $this->view->getTemplate('event-list-item.css') . ' .cibul-event-list-item .cibul-locations { display: none; }' . '</style>' . $content;
    }

    /**
     * 1. picks up cibul event links in content,
     * 2. gets the corresponding renders in the cache or generates it from cibul api data
     * 3. replaces the links in the content with event renders
     */

    public function renderEvents($content = '')
    {
      // get the event links paired with the tags (those tags will be replaced in the content)
      $eventLinkTagPairs = $this->grabEventLinkTagPairs($content);

      // get the event links in an array
      $eventLinks = $this->extractLinks($eventLinkTagPairs);

      // check which of those are stored in the plugin cache and retrieve them
      $rendersByLink = $this->cache->getRendersByLink($eventLinks);


      // for the remaining links (those not in the cache), fetch the data and render.
      $eventLinks = array_diff($eventLinks, array_keys($rendersByLink));

      $newRendersByLink = array();

      if (!empty($eventLinks))
      {
        $newDataByLink = $this->getEventsDataByLink($eventLinks);

        // if there was an error getting the data, do not convert links.

        if ($newDataByLink)
        {
          foreach ($newDataByLink as $link => $data)
          {
            $newRendersByLink[$link] = CibulPluginView::get('event_list_item', $this->formatEventListItemData($data));

            $listItemData = $this->formatEventListItemData($data); // get the template data

            // this is wrong, how is this possible?
            $listItemString = $this->view->getTwig('event-list-item.html', $listItemData['template']); // get the list item render

            $newRendersByLink[$link] = CibulPluginView::get('event_list_item_wrapper', array_merge($listItemData['wrapper'], array('listItem' => $listItemString))); // get it all
          }  

          $this->cache->loadRendersByLink($newRendersByLink);  
        }
      }


      // replace links in content with renders

      $rendersByLink = array_merge($rendersByLink, $newRendersByLink);

      // shove it back in the content where we need

      foreach ($eventLinkTagPairs as $pair)
      {
        if (isset($rendersByLink[$pair['link']])) $content = str_replace($pair['tag'], $rendersByLink[$pair['link']], $content);
      }


      // shove in some style man

      $content = $this->renderStyle($content);

      return $content;
    }

    /**
     * picks data received from db and formats it to fit in well with template
     */

    protected function formatEventListItemData($data, $options = null)
    {
      if (is_null($options)) $options = $this->options;

      if (array_key_exists($this->lang, $data['title']))
      {
        $lang = $this->lang;
      }
      else
      {
        $langs = array_keys($data['title']);

        $lang = $langs[0];
      }

      $formattedData = array(
        'template' => array(
          'title' => $data['title'][$lang],
          'description' => $data['description'][$lang],
          'freeText' => $data['freeText'][$lang],
          'spaceTimeInfo' => $data['spacetimeinfo'],
          'imageThumb' => $data['imageThumb'],
          'image' => $data['image'],
          'link' => $data['link'],
          'hasImage' => strlen($data['imageThumb'])?true:false,
          'mapIconClass' => 'js_cibul_map_icons'
          ),
        'wrapper' => array(
          'locations' => array()
        )
      );

      // location data goes in the wrapper.

      foreach($data['locations'] as $location)
      {
        $formattedData['wrapper']['locations'][] = array(
          'placename' => $location['placename'], 
          'address' => $location['address'], 
          'longitude' => $location['longitude'], 
          'latitude' => $location['latitude'],
          'slug' => $location['slug']
        );
      }

      return $formattedData;
    }

    /**
     * fetch event data for input links, indexed by link
     */

    protected function getEventsDataByLink($links)
    {
      if (empty($links)) return;
      if (is_null($this->cibulClient)) return;

      // store data in array indexed by uid

      $dataByUid = array();

      // get event uids

      $eventUids = array();

      foreach ($links as $link)
      {
        if ($uid = $this->cibulClient->getEventUidFromLink($link))
        {
          $eventUids[] = $uid;

          $dataByUid[$uid] = array('link' => $link);
        }
      }

      // fetch event data and process with template function

      $eventData = $this->cibulClient->getEvents($eventUids, $this->lang);

      if ($eventData === false) return false;

      if (!empty($eventData))
      {
        foreach ($eventData as $listItemData)
        {
          $dataByUid[$listItemData['uid']]['data'] = $listItemData;
        }

        // return data indexed by link

        $dataByLink = array();

        foreach($dataByUid as $listItemData)
        {
          $dataByLink[$listItemData['link']] = $listItemData['data'];
        }

        return $dataByLink;
      }

      return array();
    }

    /**
     * grab all event links in passed content, paired with their full tag strings
     */

    protected function grabEventLinkTagPairs($content)
    {
      $post_links = array();
      $pairs = array();

      // grab anything that begins with <a and finishes with the first a/>
      preg_match_all('/<a(.*?(\/a>))/', $content, $post_links);

      // grab contents of href where cibul.net/event is found

      foreach ($post_links[0] as $link)
      {
        if (preg_match('/cibul.net\/event\//', $link))
        {
          $href = array();

          // there is something in an href
          if (preg_match('/href="(.*?("))/', $link, $href))
          {
            $pairs[] = array('link' => substr($href[1], 0, -1), 'tag' => $link);
          }          
        }
      }

      return $pairs;
    }

    /**
     * gets links from event links/tag pairs and returns them in array
     */

    protected function extractLinks($eventLinkTagPairs)
    {
      $links = array();

      foreach($eventLinkTagPairs as $pair)
      {
        $links[] = $pair['link'];
      }

      return $links;
    }

  }