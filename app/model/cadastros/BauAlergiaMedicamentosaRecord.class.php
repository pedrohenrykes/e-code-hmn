<?php

class BauAlergiaMedicamentosaRecord extends TRecord
{
    const TABLENAME  = "baualergiamedicamentosa";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $principioativo;

    public function get_principioativo_nome()
    {
        if ( empty( $this->principioativo ) ) {
            $this->principioativo = new PrincipioAtivoRecord( $this->principioativo_id );
        }

        return $this->principioativo->nomeprincipioativo;
    }
}
