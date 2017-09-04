<?php

class BauRecord extends TRecord
{
    const TABLENAME  = "bau";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $paciente;

    public function get_paciente_nome()
    {
        if ( empty( $this->paciente ) ) {
            $this->paciente = new PacienteRecord( $this->paciente_id );
        }

        return $this->paciente->nomepaciente;
    }
}
