<?php
  set_time_limit(0);
  ini_set('display_errors', true);
  error_reporting(E_ALL);

  define('__ROOT__', dirname(__FILE__) . '/..');
  require_once(__ROOT__.'/config/mysql.php');
  require_once(__ROOT__.'/lib/common.php');

  mb_internal_encoding("UTF-8");
  $db = new mysqli($db_host, $db_user, $db_pass, $db_name);
  $db->query("SET CHARACTER SET 'utf8mb4'");
  $db->query("SET collation_connection = 'utf8mb4_unicode_ci'");
  $db->query("SET NAMES 'utf8mb4_unicode_ci'");

  $csv = file_get_contents('https://raw.githubusercontent.com/zapret-info/z-i/master/dump.csv');
  $lines = explode("\n", $csv);

  $db->query("TRUNCATE TABLE blocked");
  $inserts = array();
  $inserted = 0;
  foreach ($lines as $i => $line) {
    if ($i == 0) {
      continue;
    }

    $components = explode(';', $line);
    if (count($components) < 6) {
      continue;
    }

    $ips = explode(' | ', $components[0]);
    $link = $components[1];
    $page = $components[2];
    $gos_organ = mb_convert_encoding($components[3], 'utf-8', 'windows-1251');
    $postanovlenie = mb_convert_encoding($components[4], 'utf-8', 'windows-1251');
    $date = $components[5];

    foreach ($ips as $j => $ip) {
      if (trim($ip) == '') {
        $ip = $link;
      }
      $pair = explode('/', $ip);
      $ipFirst = ip2long($pair[0]);
      if (count($pair) > 1) {
        $length = intval($pair[1]);
        $ipLast = $ipFirst | (1 << (32 - $length)) - 1;
      } else {
        $length = 32;
        $ipLast = $ipFirst;
      }
      $inserts[] = array(
        'ip' => $ip,
        'ip_first' => $ipFirst,
        'ip_last' => $ipLast,
        'length' => $length,
        'date' => $date,
        'gos_organ' => $gos_organ,
        'postanovlenie' => $postanovlenie,
        'link' => $link,
        'page' => $page,
      );

      if (count($inserts) == 10000) {
        insertInto('blocked', $inserts);
        $inserted += count($inserts);
        $inserts = array();

        echo "Inserted {$inserted}<br/>";
      }
    }
  }

  if (count($inserts) > 0) {
    insertInto('blocked', $inserts);
    $inserted += count($inserts);
  }

  echo "Total: {$inserted}";