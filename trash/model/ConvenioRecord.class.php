<?php
/**
 *
 * @author Danilo Cunha
 */
class ConvenioRecord extends TRecord
{
    const TABLENAME  = "convenio";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";
    
    public function __construct( $id = NULL )
    {
        parent::__construct( $id );

        parent::addAttribute( "unidadeespecialidade_id" );
        parent::addAttribute( "nome" );        
    }
}
