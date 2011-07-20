<?php
/**
 * @see Zend_Db_Statement
 */
require_once 'Zend/Db/Statement.php';

class Zend_Db_Statement_Mysqli_Unprepared extends Zend_Db_Statement
{
    protected $_fetchMode, $_result;

    public function __construct($result, $fetchMode)
    {
        $this->_result    = $result;
        $this->_fetchMode = $fetchMode;
    }

    public function fetchAll($fetchMode = null)
    {
        $data = array();
        while ($row = $this->fetch($fetchMode)) {
            array_push($data, $row);
        }
        return $data;
    }

    public function fetchColumn($col = 0)
    {
        $row = $this->fetch(Zend_Db::FETCH_NUM);
        if (isset($row[$col])) {
            return $row[$col];
        }
        return false; // FIXME
    }

    public function fetch($fetchMode = null)
    {
        if ($fetchMode === null) {
            $fetchMode = $this->_fetchMode;
        }

        switch ($fetchMode) {
            default:
            case Zend_Db::FETCH_BOTH:
                $fetchMode = MYSQLI_BOTH;
                break;

            case Zend_Db::FETCH_ASSOC:
                $fetchMode = MYSQLI_ASSOC;
                break;

            case Zend_Db::FETCH_NUM:
                $fetchMode = MYSQLI_NUM;
                break;
        }

        return $this->_result->fetch_array($fetchMode);
    }

    public function __call($method, $args)
    {
        die("$method");
    }
}
