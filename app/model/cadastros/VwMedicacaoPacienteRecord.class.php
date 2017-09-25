<?php

class VwMedicacaoPacienteRecord extends TRecord
{
    const TABLENAME  = "vw_medicacao_paciente";
    const PRIMARYKEY = "bau_id";
    const IDPOLICY   = "serial";

    private $paciente;

    public function get_nomepacientecor()
    {
        if ( empty( $this->paciente ) ) {
            $this->paciente = new VwBauPacientesRecord( $this->bau_id );
        }

        return (
            '<table><tr><th style="padding:0 7px 0;"><div style="border-style:solid;border-width:7px 17px;border-color:' .
            $this->paciente->cortipoclassificacaorisco .
            ';"></div></th><th style="padding:0;"><div style="font-size:14px;font-weight:bold;">' .
            $this->paciente->nomepaciente .
            '</div></th></tr></table>'
        );
    }
}
