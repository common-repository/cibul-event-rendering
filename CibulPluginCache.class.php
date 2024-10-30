<?php

  class CibulPluginCache
  {
    public function __construct($tableName, $dbHandler)
    {
      $this->tableName = $tableName;

      $this->dbHandler = $dbHandler;
    }

    public function getRendersByLink($links, $type = '')
    {
      if (!is_array($links)) $links = array($links);

      if (empty($links)) return array();

      $result = $this->dbHandler->getQueryResults("SELECT link, html FROM {$this->tableName} WHERE link IN ('" . implode("','", $links) . "') AND type = '$type'");

      $rendersByLink = array();

      if (!empty($result)) foreach ($result as $row)
      {
        if ($row['html']) $rendersByLink[$row['link']] = $row['html'];
      }

      return $rendersByLink;
    }

    public function loadRendersByLink($rendersByLink, $type = '')
    {
      // update pre-existing renders

      $fromDb = $this->getRendersByLink(array_keys($rendersByLink), $type);

      $rendersByLinkToUpdate = array_intersect_key($fromDb, $rendersByLink);

      foreach ($rendersByLinkToUpdate as $link => $renderToUpdate)
      {
        $this->dbHandler->query("UPDATE {$this->tableName} SET html = $renderToUpdate WHERE link = $link AND type = $type");
      }

      // insert in cache table new renders

      $rendersByLinkToInsert = array_diff_key($rendersByLink, $rendersByLinkToUpdate);

      $now = new Datetime('now');

      foreach ($rendersByLinkToInsert as $link => $renderToInsert)
      {
        $this->dbHandler->insert($this->tableName, array('html' => $renderToInsert, 'link' => $link, 'type' => $type, 'created_at' => $now->format("Y-m-d H:i:s")));
      }
    }

    public function createTable()
    {
      $this->dbHandler->create("CREATE TABLE {$this->tableName} (id mediumint(9) NOT NULL AUTO_INCREMENT, created_at datetime NOT NULL, link VARCHAR(255) NOT NULL, type VARCHAR(32), html text NOT NULL, UNIQUE KEY id (id) );");
    }
    

    public function clear()
    {
      $this->dbHandler->query("DROP TABLE $this->tableName ");

      $this->createTable();

      return true;
    }
  }