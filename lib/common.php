<?php

  function insertInto($table, $vals, $keys = false) {
    global $db;

    if (empty($vals)) {
      return false;
    }

    if (!isset($vals[0]) || !is_array($vals[0])) {
      $vals = array($vals);
    }

    $dups = array();
    $fields = array();
    foreach ($vals[0] as $key => $value) {
      $fields[] = $key;
    }
    if ($keys) {
      foreach ($keys as $key => $value) {
        if (!is_int($key)) {
          if ($value == 'UPD') {
            $dups[] = "`$key` = VALUES(`$key`)";
          } else
          if ($value == 'INC') {
            $dups[] = "`$key` = `$key` + 1";
          } else
          if ($value == 'ADD') {
            $dups[] = "`$key` = `$key` + VALUES(`$key`)";
          } else
          if ($value == 'MAX') {
            $dups[] = "`$key` = GREATEST(`$key`, VALUES(`$key`))";
          } else
          if ($value == 'MIN') {
            $dups[] = "`$key` = LEAST(`$key`, VALUES(`$key`))";
          } else
          if ($value) {
            $dups[] = "`$key` = $value";
          }
          $fields[] = "$key";
        } else {
          $fields[] = "$value";
        }
      }
    }

    $fields = array_unique($fields);

    $rows = array();
    foreach ($vals as $val) {
      $row = array();
      foreach ($fields as $key) {
        if (isset($val[$key]) && ($val[$key] !== null)) {
          $row[] = "'" . $db->escape_string($val[$key]) . "'";
        } else {
          $row[] = "NULL";
        }
      }
      $rows[] = "(" . implode(",", $row) . ")";
    }

    foreach ($fields as $i => &$field) {
      $fields[$i] = "`$field`";
    }

    $db->query(
      "INSERT INTO {$table} (" . implode(", ", $fields) . ")".
        " VALUES " . implode(", ", $rows) .
        (empty($dups) ? "" : (" ON DUPLICATE KEY UPDATE " . implode(", ", $dups)))
    );
    print_r($db->error);

    return $db->insert_id;
  }

  function select($table, $where, $limit = 0, $order = '', $total = false) {
    global $db;
    $assoc = false;
    $cond = array();
    foreach ($where as $key => $value) {
      if (is_array($value)) {
        $opts = array();
        foreach ($value as $v) {
          $opts[] = "'" . $db->escape_string($v) . "'";
        }

        $cond[] = $key . ' IN (' . implode(',', $opts) . ')';
        $assoc = ($limit === 0) ? $key : false;
      } else {
        $cond[] = $key . ' = ' . ((isset($value) && ($value !== null)) ?
          "'" . $db->escape_string($value) . "'" : "NULL");
      }
    }
    $result = $db->query("SELECT * FROM {$table}" .
      (empty($cond) ? "" : (" WHERE " . implode(' AND ', $cond))) .
      (empty($order) ? "" : (" ORDER BY " . $order)) .
      (($limit === false) || $assoc ? "" : (" LIMIT " . (is_string($limit) ? $limit : max($limit, 1)))));

    if (!$result) {
      /*error_log($db->error);
      error_log("SELECT * FROM {$table}" .
      (empty($cond) ? "" : (" WHERE " . implode(' AND ', $cond))) .
      (empty($order) ? "" : (" ORDER BY " . $order)) .
      ($limit === false ? "" : (" LIMIT " . max($limit, 1))));*/
      /*echo "SELECT * FROM {$table}" .
      (empty($cond) ? "" : (" WHERE " . implode(' AND ', $cond))) .
      (empty($order) ? "" : (" ORDER BY " . $order)) .
      ($limit === false ? "" : (" LIMIT " . max($limit, 1)));
      print_r($db->error);*/
      return false;
    }


    $rows = array();
    while ($row = $result->fetch_assoc()) {
      if ($assoc) {
        $rows[$row[$assoc]] = $row;
      } else {
        $rows[] = $row;
      }
    }
    $resp = (($limit === 0) && !$assoc ? (empty($rows) ? false : $rows[0]) : $rows);
    if ($total) {
      $result = $db->query("SELECT COUNT(*) AS total FROM {$table}" .
        (empty($cond) ? "" : (" WHERE " . implode(' AND ', $cond))));
      $row = $result->fetch_assoc();
      $resp = array($resp, intval($row['total']));
    }

    return $resp;
  }
