<?php

class TipoClassificacaoRiscoForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Tipo Classificação de Risco" );
        parent::setSize( 0.600, 0.900 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_tipo_classificacao_risco" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id                         = new THidden( "id" );
        $ordem                      = new TEntry("ordem");
        $situacao                   = new TCombo("situacao");
        $nometipoclassificacaorisco = new TEntry( "nometipoclassificacaorisco" );
        $cortipoclassificacaorisco  = new TCombo( "cortipoclassificacaorisco" );
        $tempoparaatendimento       = new TEntry("tempoparaatendimento");
        $tempoparaatendimento->setProperty('type', 'time');
        
        $situacao->setDefaultOption( "..::SELECIONE::.." );
        $situacao->addItems(["ATIVO" => "ATIVO", "INATIVO" => "INATIVO"]);
        
        $cortipoclassificacaorisco->setDefaultOption( "..::SELECIONE::.." );
        $cortipoclassificacaorisco->addItems(['#0000FF' => 'Azul', '#008000' => 'Verde', '#FFFF00' => 'Amarelo', '#FF0000' => 'Vermelho']);

        $ordem->setProperty("title", "O campo e obrigatorio");
        $situacao->setProperty("title", "O campo e obrigatorio");
        $tempoparaatendimento->setProperty("title", "O campo e obrigatorio");
        $cortipoclassificacaorisco->setProperty("title", "O campo e obrigatorio");
        $nometipoclassificacaorisco->setProperty("title", "O campo e obrigatorio");
        
        $ordem->setSize("38%");
        $situacao->setSize("38%");
        $tempoparaatendimento->setSize("38%");
        $cortipoclassificacaorisco->setSize("38%");
        $nometipoclassificacaorisco->setSize("38%");
        
        $tempoparaatendimento->setMask( "hh:ii" );
        //$tempoparaatendimento->setValue( date( "H:i" ) );
        
        $this->form->addFields([new TLabel("Ordem:    $redstar")], [$ordem]);
        $this->form->addFields([new TLabel("Situação: $redstar")], [$situacao]);
        $this->form->addFields([new TLabel("Tipo Classificação Risco: $redstar")], [$nometipoclassificacaorisco]);
        $this->form->addFields([new TLabel("Cor Tipo Classificação Risco:  $redstar")], [$cortipoclassificacaorisco]);
        $this->form->addFields([new TLabel("Tempo para Atendimento: ")], [$tempoparaatendimento]);
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );
    }
    
    public function onSave()
    {
        try {

            $this->form->validate();
            
            TTransaction::open( "database" );
                $object = $this->form->getData("TipoClassificacaoRiscoRecord");
                $object->store();
            TTransaction::close();

            $action = new TAction( [ "TipoClassificacaoRiscoList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onEdit( $param )
    {
        try {

            if( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );
                    $object = new TipoClassificacaoRiscoRecord($param["key"]);
                    $this->form->setData($object);
                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
