<?php

class MedicamentoRecord extends TRecord {

    const TABLENAME = 'medicamento';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'serial';
    
    private $principioativo;
    public function get_principioativo()
    {
    	  if ( empty( $this->principioativo ) ) 
          {
            $this->principioativo = new PrincipioAtivoRecord( $this->principioativo_id );
         }
        return $this->principioativo->nomeprincipioativo;
    }

}

