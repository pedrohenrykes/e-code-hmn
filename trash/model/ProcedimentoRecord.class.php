<?php

use Adianti\Database\TRecord;

/**
 *
 * @author Danilo Cunha
 */
class ProcedimentoRecord extends TRecord
{
    const TABLENAME = "procedimento";
    const PRIMARYKEY = "id";
    const IDPOLICY = "serial";
    
    public function __construct( $id = NULL )
    {
        parent::__construct( $id );

        parent::addAttribute( "nomeprocedimento" );
        
        
    }
}
