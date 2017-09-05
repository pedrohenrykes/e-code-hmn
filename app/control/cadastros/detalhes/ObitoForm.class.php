<?php

class ObitoForm extends TPage
{
    private $form;
    private $pageNavigation;
    private $loaded;
    private $changeFields;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_obito" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                       = new THidden("id");
        $paciente_id              = new THidden( "paciente_id" );
        $bau_id                   = new THidden( "bau_id" );        
        $paciente_nome            = new TEntry( "paciente_nome" );
        $dataobito                = new TDate("dataobito");
        $declaracaoobitodata      = new TDate("declaracaoobitodata");
        $horaobito                = new TDateTime("horaobito");
        $declaracaoobitohora      = new TDateTime("declaracaoobitohora");
        $medicoalta_id            = new TCombo("medicoalta_id");
        $destinoobito_id          = new TCombo("destinoobito_id");
        $declaracaoobitomedico_id = new TCombo("declaracaoobitomedico_id");

        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );

        try {

            TTransaction::open( "database" );

            $bau = new BauRecord( $fk );
            $paciente = new PacienteRecord( $did );

            if( isset( $bau ) && isset( $paciente ) ) {

                $bau_id->setValue( $bau->id );
                $paciente_id->setValue( $paciente->id );
                $paciente_nome->setValue( $paciente->nomepaciente );

            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            $action = new TAction( [ "PacientesDeclaracaoObitoList", "onReload" ] );

            new TMessage( "error", "Ocorreu um erro ao carregar as dependência do formulário.", $action );

        }

        $id                      ->setSize( "38%" );
        $paciente_nome           ->setSize( "38%" );
        $dataobito               ->setSize( "38%" );
        $horaobito               ->setSize( "38%" );
        $declaracaoobitodata     ->setSize( "38%" );
        $declaracaoobitohora     ->setSize( "38%" );
        $medicoalta_id           ->setSize( "38%" );
        $destinoobito_id         ->setSize( "38%" );
        $declaracaoobitomedico_id->setSize( "38%" );
       
        $destinoobito_id         ->setDefaultOption( "..::SELECIONE::.." );
        $declaracaoobitomedico_id->setDefaultOption( "..::SELECIONE::.." );
       
        $horaobito          ->setMask( "hh:ii" );
        $declaracaoobitohora->setMask( "hh:ii" );
        $dataobito          ->setMask( "dd/mm/yyyy" );
        $declaracaoobitodata->setMask( "dd/mm/yyyy" );
        $dataobito          ->setDatabaseMask("yyyy-mm-dd");
        $declaracaoobitodata->setDatabaseMask("yyyy-mm-dd");

        $paciente_nome->setEditable( false );
        $paciente_id->addValidation( TextFormat::set( "Sexo" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Nome do Paciente:" ) ], [ $paciente_nome ] );        
        $this->form->addFields( [ new TLabel( "Data do Óbito: {$redstar}" ) ], [ $dataobito ] );
        $this->form->addFields( [ new TLabel( "Hora do Óbito:{$redstar}" ) ], [ $horaobito ] );
        $this->form->addFields( [ new TLabel( "Data da Declaração:{$redstar}" ) ], [ $declaracaoobitodata ] );
        $this->form->addFields( [ new TLabel( "Hora da Declaração:{$redstar}" ) ], [ $declaracaoobitohora ] );
        $this->form->addFields( [ new TLabel( "Destino do Corpo:{$redstar}" ) ], [ $destinoobito_id ] );
        $this->form->addFields( [ new TLabel( "Medico Responsável:{$redstar}" ) ], [ $declaracaoobitomedico_id ] );
       

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "did", $did );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar para Pacientes", new TAction( [ "PacientesDeclaracaoObitoList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave( $param = null )
    {
        $object = $this->form->getData( "BauRecord" );

        try {

            $this->form->validate();

            TTransaction::open( "database" );
            
            unset($object->paciente_nome);
            
            $object->situacao = 'OBITO';

            $object->store();

            TTransaction::close();

            $action = new TAction( [ "PacientesDeclaracaoObitoList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->form->setData( $object );

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }
    
    public function onReload(){}
     
}
