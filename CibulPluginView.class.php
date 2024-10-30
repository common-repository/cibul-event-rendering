<?php

  require_once('lib/Twig/Autoloader.php');

  class CibulPluginView
  {
    const TEMPLATE_FOLDER = 'templates/';

    public function __construct($templates)
    {
      Twig_Autoloader::register();

      $loader = new Twig_Loader_String();

      $this->twig = new Twig_Environment($loader, array('autoescape' => false));

      $this->templates = $templates;
    }


    /**
     * render contents of template
     */
    
    public static function render($templateName, $data)
    {
      extract($data);

      include(self::getTemplatePath($templateName));
    }


    /**
     * return contents of template as string
     */

    public static function get($templateName, $data)
    {
      extract($data);
        
        ob_start();
        
        include(self::getTemplatePath($templateName, isset($extension)?$extension:'php'));

        return ob_get_clean();

      return false;
    }


    /**
     * return contents of twig template
     */

    public function getTwig($templateName, $data = array())
    {
      if (isset($this->templates[$templateName]))
      {
        return $this->twig->render($this->templates[$templateName], $data);  
      }

      return $this->twig->render($this->get($templateName, array('extension' => false)), $data);
    }

    public function getTemplate($templateName)
    {
      return $this->templates[$templateName];
    }

    protected static function getTemplatePath($templateName, $extension = 'php')
    {
      return self::TEMPLATE_FOLDER . $templateName . ($extension?'.'.$extension:'');
    }
  }