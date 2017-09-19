<?php

class VwBauPacientesRecord extends TRecord
{
    const TABLENAME  = "vw_bau_pacientes";
    const PRIMARYKEY = "bau_id";
    const IDPOLICY   = "serial";

    private $paciente;

    public function get_nomepacientecor()
    {
        if ( empty( $this->paciente ) ) {
            $this->paciente = new VwBauPacientesRecord( $this->bau_id );
        }

        return (
            '<table><tr><th><div style="border-style:solid;border-width:7px 17px;border-color:' .
            $this->paciente->cortipoclassificacaorisco .
            ';"></div></th><th><div style="font-size:14px;font-weight:bold;">' .
            $this->paciente->nomepaciente .
            '</div></th></tr></table>'
        );
    }
}
