<?php

class DeclaracaoObitoDetail extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Óbito de Pacientes" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "detail_declaracao_obito" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                       = new THidden("id");
        $paciente_id              = new THidden( "paciente_id" );
        $paciente_nome            = new TEntry( "paciente_nome" );
        $dataobito                = new TDate("dataobito");
        $declaracaoobitodata      = new TDate("declaracaoobitodata");
        $horaobito                = new TDateTime("horaobito");
        $declaracaoobitohora      = new TDateTime("declaracaoobitohora");
        $destinoobito_id          = new TCombo("destinoobito_id");
        $declaracaoobitomedico_id = new TCombo("declaracaoobitomedico_id");

        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );

        try {

            TTransaction::open( "database" );

            $bau = new BauRecord( $fk );
            $paciente = new PacienteRecord( $did );

            if( isset( $bau ) && isset( $paciente ) ) {

                $id->setValue( $bau->id );
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

        $dataobito->addValidation( TextFormat::set( "Nome do Paciente" ), new TRequiredValidator );
        $horaobito->addValidation( TextFormat::set( "Data do Óbito" ), new TRequiredValidator );
        $declaracaoobitodata->addValidation( TextFormat::set( "Data da Declaração" ), new TRequiredValidator );
        $declaracaoobitohora->addValidation( TextFormat::set( "Hora da Declaração" ), new TRequiredValidator );
        // $destinoobito_id->addValidation( TextFormat::set( "Destino do Corpo" ), new TRequiredValidator );
        // $declaracaoobitomedico_id->addValidation( TextFormat::set( "Medico Responsável" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Nome do Paciente:" ) ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Data do Óbito:{$redstar}" ) ], [ $dataobito ] );
        $this->form->addFields( [ new TLabel( "Hora do Óbito:{$redstar}" ) ], [ $horaobito ] );
        $this->form->addFields( [ new TLabel( "Data da Declaração:{$redstar}" ) ], [ $declaracaoobitodata ] );
        $this->form->addFields( [ new TLabel( "Hora da Declaração:{$redstar}" ) ], [ $declaracaoobitohora ] );
        $this->form->addFields( [ new TLabel( "Destino do Corpo:{$redstar}" ) ], [ $destinoobito_id ] );
        $this->form->addFields( [ new TLabel( "Medico Responsável:{$redstar}" ) ], [ $declaracaoobitomedico_id ] );
        $this->form->addFields( [ $id, $paciente_id ] );


        $onSave   = new TAction( [ $this, "onSave" ] );
        $onReload = new TAction( [ "PacientesDeclaracaoObitoList", "onReload" ] );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar", $onReload, "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave( $param = null )
    {

        try {

            $this->form->validate();

            $object = $this->form->getData( "BauRecord" );

            TTransaction::open( "database" );

            unset($object->paciente_nome);
            $object->situacao = "OBITO";
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "PacientesDeclaracaoObitoList", "onReload" ] );
            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            // $this->form->setData( $object );

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onReload(){}

}
