<?php

class EncaminhamentoDetail extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $changeFields;

    public function __construct()
    {
        parent::__construct();
        // parent::setTitle( "Encaminhamento de Pacientes" );
        // parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "detail_encaminhamento" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                       = new THidden("id");
        $paciente_id              = new THidden( "paciente_id" );
        $paciente_nome            = new TEntry( "paciente_nome" );
        $datainternamento         = new TDate("datainternamento");
        $dataremocao              = new TDate("dataremocao");
        $localremocao_id          = new TCombo("localremocao_id");
        $datatransferencia        = new TDate("datatransferencia");
        $localtransferencia_id    = new TCombo("localtransferencia_id");
        $transportedestino_id     = new TCombo("transportedestino_id");
        $especificartransporte    = new TEntry("especificartransporte");
        $datatransporte           = new TDate("datatransporte");

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

            $action = new TAction( [ "PacientesEncaminhamentoList", "onReload" ] );

            new TMessage( "error", "Ocorreu um erro ao carregar as dependência do formulário.", $action );

        }

        $id                      ->setSize( "38%" );
        $paciente_nome           ->setSize( "38%" );
        $datainternamento        ->setSize( "38%" );
        $dataremocao             ->setSize( "38%" );
        $localremocao_id         ->setSize( "38%" );
        $datatransferencia       ->setSize( "38%" );
        $localtransferencia_id   ->setSize( "38%" );
        $transportedestino_id    ->setSize( "38%" );
        $especificartransporte   ->setSize( "38%" );
        $datatransporte          ->setSize( "38%" );

        $localremocao_id         ->setDefaultOption( "..::SELECIONE::.." );
        $localtransferencia_id   ->setDefaultOption( "..::SELECIONE::.." );
        $transportedestino_id    ->setDefaultOption( "..::SELECIONE::.." );

        $datainternamento   ->setMask( "dd/mm/yyyy" );
        $dataremocao        ->setMask( "dd/mm/yyyy" );
        $datatransferencia  ->setMask( "dd/mm/yyyy" );
        $datatransporte     ->setMask( "dd/mm/yyyy" );

        $datainternamento   ->setDatabaseMask("yyyy-mm-dd");
        $dataremocao        ->setDatabaseMask("yyyy-mm-dd");
        $datatransferencia  ->setDatabaseMask("yyyy-mm-dd");
        $datatransporte     ->setDatabaseMask("yyyy-mm-dd");

        $paciente_nome->setEditable( false );

        $this->form->addFields( [ new TLabel( "Paciente:" ) ], [ $paciente_nome ] );

        switch( filter_input( INPUT_GET, "mode" ) ) {

            case "internamento":

                $this->form->addFields( [ new TLabel( "Data de Internamento: {$redstar}" ) ], [ $datainternamento ] );
                $datainternamento->addValidation( "Data de Internamento", new TRequiredValidator );

                break;

            case "remocao":

                $this->form->addFields( [ new TLabel( "Data de Remoção: {$redstar}") ], [ $dataremocao ] );
                $this->form->addFields( [ new TLabel( "Local de Remoção: {$redstar}") ], [ $localremocao_id ] );
                $dataremocao->addValidation( "Data de Remoção", new TRequiredValidator );
                $localremocao_id->addValidation( "Local de Remoção", new TRequiredValidator );

                break;

            case "transferencia":

                $this->form->addFields( [ new TLabel( "Data de Transferência: {$redstar}" ) ], [ $datatransferencia ] );
                $this->form->addFields( [ new TLabel( "Local de Transferência:" ) ], [ $localtransferencia_id ] );
                $this->form->addFields( [ new TLabel( "Destino do Transporte:" ) ], [ $transportedestino_id ] );
                $this->form->addFields( [ new TLabel( "Informações do Transporte:" ) ], [ $especificartransporte ] );
                $this->form->addFields( [ new TLabel( "Data do Transporte:" ) ], [ $datatransporte ] );
                $datatransferencia->addValidation( "Data de Transferência", new TRequiredValidator );

                break;

        }

        $this->form->addFields([ $paciente_id, $id ]);

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "did", $did );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar", new TAction( [ "PacientesEncaminhamentoList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onReload( $param = null )
    {
        if ( empty( filter_input( INPUT_GET, "mode" ) ) ) {

            // $action = new TAction( [ "PacientesEncaminhamentoList", "onReload" ] );

            // $this->onError();

        }
    }

    public function onSave( $param = null )
    {
        try {

            $this->form->validate();

            $object = $this->form->getData( "BauRecord" );

            TTransaction::open( "database" );

            unset($object->paciente_nome);
            $object->situacao = "ENCAMINHADO";
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "PacientesEncaminhamentoList", "onReload" ] );
            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onError()
    {
        $action = new TAction( [ "PacientesEncaminhamentoList", "onReload" ] );

        new TMessage( "error", "Uma instabilidade momentâneo no sistema impediu a ação, tente novamente mais tarde.", $action );
    }
}
