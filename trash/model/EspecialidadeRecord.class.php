<?php
class EspecialidadeRecord extends TRecord
{
    const TABLENAME  = "especialidade";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $clinica;

    public function get_clinica_nome()
    {
    	  if ( empty( $this->clinica ) ) {
            $this->clinica = new ClinicaRecord( $this->clinica_id );
        }

        return $this->clinica->nomeclinica;
    }
}
