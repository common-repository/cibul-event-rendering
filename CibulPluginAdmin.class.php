<?php

  class CibulPluginAdmin
  {
    var $optionsStoreName = 'cibul-options';
    var $options = array();
    var $defaultOptions = array(
      'key' => '',
      'keyValid' => false,
      'resource' => 'https://api.cibul.net/v1/',
      'lang' => 'en',
      'cacheTableName' => 'cibul_cache',
      'templates' => array(),
      'testUid' => 19723220 // Les Histoires Extraordinaires de Diana Thorneycroft
    );

    var $defaultTemplateNames = array('event-list-item.html', 'event-list-item.css');

    public function getOptions()
    {
      if (empty($this->options))
      {
        $storedOptions = get_option($this->optionsStoreName);

        $this->options = array_merge($this->defaultOptions, $storedOptions?$storedOptions:array());

        // load template options
        $this->options['templates'] = array_merge($this->getDefaultTemplates(), $this->options['templates']);
      }

      return $this->options;
    }

    /**
     * foreach template listed in template options, load them from template files if nothing is set yet
     */

    protected function getDefaultTemplates()
    {
      $defaultTemplates = array();

      foreach ($this->defaultTemplateNames as $defaultTemplateName)
      {
        $fh = fopen(dirname(__FILE__) . '/templates/' . $defaultTemplateName, 'r');

        $defaultTemplates[$defaultTemplateName] = fread($fh, filesize(dirname(__FILE__) . '/templates/' . $defaultTemplateName)); 
        
        fclose($fh);
      }

      return $defaultTemplates;
    }

    public function setCache($cache)
    {
      $this->cache = $cache;
    }

    protected function clearCache()
    {
      if (isset($this->cache)) 
      {
        // update sample
        
        return $this->cache->clear();
      }

      return false;
    }

    public function getConnectionOptions()
    {
      $options = $this->getOptions();

      return array(
        'key' => $options['key'],
        'resource' => $options['resource'], 
        'lang' => $options['lang']
      );
    }

    public function setOptions($options)
    {
      $this->options = array_merge($this->getOptions(), $options);

      update_option($this->optionsStoreName, $this->options);
    }

    public function processRequest()
    {
      $updated = false; 
      $cache_cleared = false;

      if (isset($_POST['update_cibul_settings'])) $updated = $this->processSubmit();

      if (isset($_POST['update_cibul_list_item_template'])) $updated = $updated || $this->processTemplateSubmit('cibul_event_list_item_template', 'event-list-item.html');

      if (isset($_POST['update_cibul_list_item_css'])) $updated = $updated || $this->processTemplateSubmit('cibul_event_list_item_css', 'event-list-item.css');

      $options = $this->getOptions();

      if ($updated || isset($_POST['clear_cache']))
      {
        $this->clearCache();

        if (isset($_POST['clear_cache'])) $cache_clear_message = true;
      }

      CibulPluginView::render('admin_menu', array(
        'options' => $options, 
        'updated' => $updated, 
        'cache_clear_message' => $cache_clear_message, 
        'default_templates' => $options['templates'],
      ));
    }
    

    public function processSubmit()
    {
      $options = $this->getOptions();

      $newOptions = array();

      $toUpdate = false;

      // look at the api key

      if (isset($_POST['cibul_key'])) if ($_POST['cibul_key'] != $options['key'])
      {
        // check the validity of the input key here with a test event uid.

        $newOptions['key'] = $_POST['cibul_key'];

        $newOptions['keyValid'] = $this->verifyKey($newOptions['key']);

        $toUpdate = true;
      }

      // and the preferred language

      if (isset($_POST['cibul_lang'])) if ($_POST['cibul_lang'] != $options['lang'])
      {
        // check input value
        if (in_array($_POST['cibul_lang'], array('en', 'fr')))
        {
          $newOptions['lang'] = $_POST['cibul_lang'];

          $toUpdate = true;
        }
      }

      if ($toUpdate) $this->setOptions($newOptions);

      return $toUpdate;
    }

    protected function processTemplateSubmit($fieldName, $templateName)
    {
      $newTemplates = array();

      $options = $this->getOptions();

      if (isset($_POST[$fieldName]))
      {
        if ($_POST[$fieldName] != $options['templates'][$templateName])
        {
          // set new template

          $templates = array($templateName => stripslashes($_POST[$fieldName]));

          // fill in with other templates

          foreach ($options['templates'] as $name => $template)
          {
            if (!isset($templates[$name])) $templates[$name] = $template;
          }

          // save in options

          $this->setOptions(array('templates' => $templates));

          return true;
        }
      }

      return false;
    }

    protected function verifyKey($key)
    {
      $options = $this->getOptions();

      try
      {
        $cibulClient = new CibulClientSDK($key);
      }
      catch (Exception $e)
      {
        return false;
      }

      $result = $cibulClient->getEvents(array($options['testUid']));
      
      return $result?true:false;
    }


    public function isApiKeyValid()
    {
      $options = $this->getOptions();

      return $options['keyValid'];
    }


  }
