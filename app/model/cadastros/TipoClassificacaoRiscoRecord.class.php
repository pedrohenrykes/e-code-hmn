<?php

class TipoClassificacaoRiscoRecord extends TRecord
{
    const TABLENAME  = "tipoclassificacaorisco";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";
    
    function get_nomecor ()
    {
        return '<font color="'.$this->cortipoclassificacaorisco.'">'.$this->nometipoclassificacaorisco.'</font>';
    }
}
