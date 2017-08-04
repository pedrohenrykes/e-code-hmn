<?php

class ClinicaRecord extends TRecord
{
    const TABLENAME = "clinica";
    const PRIMARYKEY = "id";
    const IDPOLICY = "serial";

    private $ies;

    public function get_ies_nome()
    {
        if ( empty( $this->ies ) ) {
            $this->ies = new IesRecord( $this->ies_id );
        }

        return $this->ies->nomeies;
    }
}
