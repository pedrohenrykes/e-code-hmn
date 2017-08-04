<?php

class CidRecord extends TRecord
{
    const TABLENAME = 'cid';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('nomecid');
        parent::addAttribute('codigocid');
    }
}