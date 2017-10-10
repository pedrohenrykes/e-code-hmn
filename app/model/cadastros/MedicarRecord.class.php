<?php

class MedicarRecord extends TRecord
{
    const TABLENAME  = "baumedicar";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $medicamento;
    private $datap;
    
    function get_medicamento_nome(){

        if (empty ($this->medicamento)){
            $this->medicamento = new MedicamentoRecord($this->medicamento_id);
        }
        
        return $this->medicamento->nomemedicamento;

    }    
    
    function get_data_prescricao(){

        if (empty ($this->datap)){
            $this->datap = new BauPrescricaoRecord($this->bauprescricao_id);

        }
        
        return $this->datap->data_prescricao;

    }

}
