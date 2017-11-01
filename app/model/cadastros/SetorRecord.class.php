<?php

class SetorRecord extends TRecord
{
    const TABLENAME  = "setor";
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
    private $pacienteid;

    public function get_paciente_id2()
    {
        if ( empty( $this->pacienteid ) ) {
            $this->pacienteid = new PacienteRecord( $this->paciente_id );
        }

        return $this->pacienteid->id;
    }
}
