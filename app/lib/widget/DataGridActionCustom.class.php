<?php

use Adianti\Widget\Datagrid\TDataGridAction;

class DataGridActionCustom extends TDataGridAction
{
    private $fk;
    private $did;

    public function setFk($field)
    {
        $this->fk = $field;
    }

    public function getFk()
    {
        return $this->fk;
    }

    public function setDid($field)
    {
        $this->did = $field;
    }

    public function getDid()
    {
        return $this->did;
    }
}
