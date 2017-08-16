<?php

class ProfissionalRecord extends TRecord
{
    const TABLENAME  = "profissional";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";
    
    private $tipoprofissional;
    public function get_tipo_profissional()
    {
    	  if ( empty( $this->tipoprofissional ) ) 
          {
            $this->tipoprofissional = new TipoProfissionalRecord( $this->tipoprofissional_id );
         }
        return $this->tipoprofissional->nometipoprofissional;
    }
    
}

   
