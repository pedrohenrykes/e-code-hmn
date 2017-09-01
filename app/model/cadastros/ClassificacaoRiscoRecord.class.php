<?php

class ClassificacaoRiscoRecord extends TRecord
{
    const TABLENAME  = "classificacaorisco";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $paciente;
    private $tipoclassificacaorisco;
    private $enfermeiro;

    public function get_paciente_nome()
    {
        if ( empty( $this->paciente ) ) {
            $this->paciente = new PacienteRecord( $this->paciente_id );
        }

        return $this->paciente->nomepaciente;
    }

    public function get_tipoclassificacaorisco_nome()
    {
        if ( empty( $this->tipoclassificacaorisco ) ) {
            $this->tipoclassificacaorisco = new TipoClassificacaoRiscoRecord( $this->tipoclassificacaorisco_id );
        }

        return $this->tipoclassificacaorisco->nometipoclassificacaorisco;
    }

    public function get_enfermeiro_nome()
    {
        if ( empty( $this->enfermeiro ) ) {
            $this->enfermeiro = new ProfissionalRecord( $this->enfermeiro_id );
        }

        return $this->paciente->nomeprofissional;
    }
}
