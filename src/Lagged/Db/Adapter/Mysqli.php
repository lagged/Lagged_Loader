<?php
class Lagged_Db_Adapter_Mysqli extends Zend_Db_Adapter_Mysqli
{
    /**
     * Default class name for a DB statement.
     *
     * @var string
     */
    protected $_defaultStmtClass = 'Lagged_Db_Statement_Mysqli';
}
