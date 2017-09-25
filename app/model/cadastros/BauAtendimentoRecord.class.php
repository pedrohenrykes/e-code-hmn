<?php

class BauAtendimentoRecord extends TRecord
{
    const TABLENAME  = "bauatendimento";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $paciente;
    private $tipoclassificacaorisco;
    private $profissional;

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

    public function get_responsavel_nome()
    {
        if ( empty( $this->profissional ) ) {
            $this->profissional = new ProfissionalRecord( $this->profissional_id );
        }

        return $this->profissional->nomeprofissional;
    }
}
