<?php

class Db
{
    private $db_connect;
    private $queries_executed;

    public function __construct()
    {
        $this->db_connect = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

        if (!$this->db_connect) {
            $this->error('Connect erro: ' . mysqli_connect_error());
            die();
        }

        $this->db_connect->set_charset('utf8');

        $this->query('SET NAMES "utf8" COLLATE "utf8_unicode_ci"');
        $this->query('SET CHARACTER SET utf8');
        $this->query('SET COLLATION_CONNECTION="utf8_unicode_ci"');

        $offset = date('P');
        $this->query('SET time_zone="' . $offset . '"');
    }

    public function query($query)
    {
        global $exectime_start;

        if (SHOW_QUERIES) {
            $exectime_start = microtime(true);
        }

        $res = mysqli_query($this->db_connect, $query);
        if (!$res) {
            $this->error('query error: ' . mysqli_error($this->db_connect) . ' query: ' . $query);
        }

        if (SHOW_QUERIES) {
            $exectime_end = microtime(true);
            $exectime = $exectime_end - $exectime_start;

            $debuginfo = debug_backtrace();

            $this->queries_executed .= '<div style="color:#8f070a;"><strong>Query:</strong> ' . $query . "</div>\n";
            $this->queries_executed .= '<strong>File/line:</strong> ' . $debuginfo[0]['file'] . ' (' . $debuginfo[0]['line'] . ")<br>\n";

            $this->queries_executed .= '<strong>Execution time:</strong> ' . $exectime . "<br><br>\n\n";
        }

        return $res;
    }

    public function getQueriesExecuted()
    {
        return $this->queries_executed;
    }

    public function fetch_row($res)
    {
        return mysqli_fetch_row($res);
    }

    public function fetch_all($res)
    {
        return mysqli_fetch_all($res, MYSQLI_NUM);
    }

    public function num_rows($res)
    {
        return mysqli_num_rows($res);
    }

    public function affected_rows()
    {
        return mysqli_affected_rows($this->db_connect);
    }

    public function insert_id()
    {
        return mysqli_insert_id($this->db_connect);
    }

    public function free_result($res)
    {
        mysqli_free_result($res);
    }

    public function data_seek($res, $row)
    {
        if ($this->num_rows($res)) {
            mysqli_data_seek($res, $row);
        }
    }

    public function escape_string($str)
    {
        return mysqli_real_escape_string($this->db_connect, $str);
    }

    public function close()
    {
        mysqli_close($this->db_connect);
    }

    private function error($err)
    {
        if (DB_DEBUG) {
            echo 'Error accured: ', $err, '<br>';
        }
    }

}
