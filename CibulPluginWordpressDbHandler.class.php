<?php

  class CibulPluginWordpressDbHandler
  {
    /**
     * query db and return results by list of associative arrays
     */

    public static function getQueryResults($query)
    {
      global $wpdb;

      $result = $wpdb->get_results($query);

      $resultArray = array();

      foreach ($result as $row)
      {
        $resultArray[] = get_object_vars($row);
      }

      return $resultArray;
    }


    /**
     * used for update and drop table queries
     */

    public static function query($query)
    {
      global $wpdb;

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      $wpdb->query($query);
    }

    /**
     * used for insert queries
     */

    public static function insert($tableName, $values)
    {
      global $wpdb;

      $wpdb->insert($tableName, $values);
    }

    /**
     * used for create table queries
     */

    public static function create($query)
    {
      global $wpdb;

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      dbDelta($query);
    }
  }