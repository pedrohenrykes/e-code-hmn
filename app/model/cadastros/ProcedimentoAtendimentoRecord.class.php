<?php

use Adianti\Database\TRecord;

/**
 *
 * @author Danilo Cunha
 */
class ProcedimentoAtendimentoRecord extends TRecord
{
    const TABLENAME = "procedimentoatendimento";
    const PRIMARYKEY = "id";
    const IDPOLICY = "serial";
    
    public function __construct( $id = NULL )
    {
        parent::__construct( $id );

        parent::addAttribute( "procedimento_id" );
        parent::addAttribute( "atendimento_id" );
        
    }
}
