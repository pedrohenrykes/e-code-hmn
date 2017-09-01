<?php

class PacienteEncaminhamentoDetalhe extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $changeFields;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "detalhe_paciente_encaminhamento_detalhe" );
        $this->form->setFormTitle( "Detalhe de Encaminhamento de Paciente" );
        $this->form->class = "tform";

        $id                       = new THidden("id");
        $paciente_id              = new THidden( "paciente_id" );
        $paciente_nome            = new TEntry( "paciente_nome" );
        $internamentolocal        = new TCombo("internamentolocal");
        $datainternamento         = new TDate("datainternamento");
        $remocao                  = new TCombo("remocao");
        $dataremocao              = new TDate("dataremocao");
        $localremocao_id          = new TCombo("localremocao_id");
        $transferencia            = new TCombo("transferencia");
        $datatransferencia        = new TDate("datatransferencia");
        $localtransferencia_id    = new TCombo("localtransferencia_id");
        $transportedestino_id     = new TCombo("transportedestino_id");
        $especificartransporte    = new TEntry("especificartransporte");
        $datatransporte           = new TDate("datatransporte");
        $convenio_id              = new TDBCombo( "convenio_id", "database", "ConvenioRecord", "id", "nome", "nome");
        
        $did = filter_input( INPUT_GET, "did" );

        try {

            TTransaction::open( "database" );

            $paciente = new PacienteRecord( $did );

            if( isset( $paciente ) ){
                $paciente_id->setValue( $paciente->id );
                $paciente_nome->setValue( $paciente->nomepaciente );
            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            new TMessage( "error", "Não foi possível carregar os dados do paciente.<br><br>" . $ex->getMessage() );

        }

        $id                      ->setSize( "38%" );
        $paciente_nome           ->setSize( "38%" );
        $internamentolocal       ->setSize( "38%" );
        $datainternamento        ->setSize( "38%" );
        $remocao                 ->setSize( "38%" );
        $dataremocao             ->setSize( "38%" );
        $localremocao_id         ->setSize( "38%" );
        $transferencia           ->setSize( "38%" );
        $datatransferencia       ->setSize( "38%" );
        $localtransferencia_id   ->setSize( "38%" );
        $transportedestino_id    ->setSize( "38%" );
        $especificartransporte   ->setSize( "38%" );
        $datatransporte          ->setSize( "38%" );
        $convenio_id             ->setSize( "38%" );

        $internamentolocal       ->setDefaultOption( "..::SELECIONE::.." );
        $remocao                 ->setDefaultOption( "..::SELECIONE::.." );
        $localremocao_id         ->setDefaultOption( "..::SELECIONE::.." );
        $transferencia           ->setDefaultOption( "..::SELECIONE::.." );
        $localtransferencia_id   ->setDefaultOption( "..::SELECIONE::.." );
        $transportedestino_id    ->setDefaultOption( "..::SELECIONE::.." );
        $convenio_id             ->setDefaultOption( "..::SELECIONE::.." );
        
        $internamentolocal->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );
        $remocao          ->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );
        $transferencia    ->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );
        
        $internamentolocal->addItems( [ "S" => "SIM", "N" => "NÃO" ] );
        $remocao          ->addItems( [ "S" => "SIM", "N" => "NÃO" ] );
        $transferencia    ->addItems( [ "S" => "SIM", "N" => "NÃO" ] );
        
        $internamentolocal->setValue( "N" );
        $remocao          ->setValue( "N" );
        $transferencia    ->setValue( "N" );
        $convenio_id      ->setValue( "5" );

        $datainternamento   ->setMask( "dd/mm/yyyy" );
        $dataremocao        ->setMask( "dd/mm/yyyy" );
        $datatransferencia  ->setMask( "dd/mm/yyyy" );
        $datatransporte     ->setMask( "dd/mm/yyyy" );
        
        $datainternamento   ->setDatabaseMask("yyyy-mm-dd");
        $dataremocao        ->setDatabaseMask("yyyy-mm-dd");
        $datatransferencia  ->setDatabaseMask("yyyy-mm-dd");
        $datatransporte     ->setDatabaseMask("yyyy-mm-dd");
        
        $this->changeFields = [ "internamentolocal", "remocao", "transferencia", "alta", "obito" ];
        
        $paciente_nome->setEditable( false );
        
        $convenio_id->addValidation( TextFormat::set( "Nome do Paciente" ), new TRequiredValidator );
        $paciente_id->addValidation( TextFormat::set( "Sexo" ), new TRequiredValidator );


        $this->form->addFields( [ new TLabel( "Paciente:" ) ], [ $paciente_nome ]);
        $this->form->addFields( [ new TLabel( "Internamento: {$redstar}" ) ], [ $internamentolocal ]);
        $this->form->addFields( [ new TLabel( "Data de Internamento: {$redstar}" ) ], [ $datainternamento ] );
        $this->form->addFields( [ new TLabel( "Remoção: {$redstar}") ], [ $remocao ] );
        $this->form->addFields( [ new TLabel( "Data de Remoção: {$redstar}") ], [ $dataremocao ] );
        $this->form->addFields( [ new TLabel( "Local de Remoção: {$redstar}") ], [ $localremocao_id ] );
        $this->form->addFields( [ new TLabel( "Transferência: {$redstar}") ], [ $transferencia ] );
        $this->form->addFields( [ new TLabel( "Data de Transferência: {$redstar}" ) ], [ $datatransferencia ] );
        $this->form->addFields( [ new TLabel( "Local de Transferência:" ) ], [ $localtransferencia_id ] );
        $this->form->addFields( [ new TLabel( "Destino do Transporte:" ) ], [ $transportedestino_id ] );
        $this->form->addFields( [ new TLabel( "Informações do Transporte:" ) ], [ $especificartransporte ] );
        $this->form->addFields( [ new TLabel( "Data do Transporte:" ) ], [ $datatransporte ] );
        $this->form->addFields( [ new TLabel("({$redstar}) campos obrigatórios") ]);
        $this->form->addFields([ $paciente_id, $id ]);

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "did", $did );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar para Encaminhamentos", new TAction( [ "PacientesEncaminhamentoList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( $this->form );

        parent::add( $container );
    }
    
        public function onReload( $param = null )
    {
        try {

            $this->loaded = true;

            foreach ( $this->changeFields as $field ) {
                self::onChangeAction([
                    "_field_name" => $field,
                    $field => "N"
                ]);
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
        }
    }

    public function onSave( $param = null )
    {
        $object = $this->form->getData( "BauRecord" );

        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object->store();

            TTransaction::close();

            $action = new TAction( [ "PacientesEncaminhamentoList", "onReload" ] );
            $action->setParameter( "did", $param[ "did" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->form->setData( $object );

            foreach ( $this->changeFields as $field ) {
                self::onChangeAction([
                    "_field_name" => $field,
                    $field => $object->$field
                ]);
            }

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public static function onChangeAction( $param = null )
    {
        $object = new StdClass;

        $fieldName = $param[ "_field_name" ];

        switch ( $fieldName ) {

            case "transferencia":

                if( $param[ $fieldName ] == "S" ) {

                    TQuickForm::showField( "detalhe_paciente_encaminhamento_detalhe", "datatransferencia" );
                    TQuickForm::showField( "detalhe_paciente_encaminhamento_detalhe", "localtransferencia_id" );
                    TQuickForm::showField( "detalhe_paciente_encaminhamento_detalhe", "transportedestino_id" );
                    TQuickForm::showField( "detalhe_paciente_encaminhamento_detalhe", "especificartransporte" );
                    TQuickForm::showField( "detalhe_paciente_encaminhamento_detalhe", "datatransporte" );

                } else {

                    $object->datatransferencia = "";
                    $object->localtransferencia_id = "";
                    $object->transportedestino_id = "";
                    $object->especificartransporte = "";
                    $object->datatransporte = "";

                    TQuickForm::sendData( "detalhe_paciente_encaminhamento_detalhe", $object );
                    TQuickForm::hideField( "detalhe_paciente_encaminhamento_detalhe", "datatransferencia" );
                    TQuickForm::hideField( "detalhe_paciente_encaminhamento_detalhe", "localtransferencia_id" );
                    TQuickForm::hideField( "detalhe_paciente_encaminhamento_detalhe", "transportedestino_id" );
                    TQuickForm::hideField( "detalhe_paciente_encaminhamento_detalhe", "especificartransporte" );
                    TQuickForm::hideField( "detalhe_paciente_encaminhamento_detalhe", "datatransporte" );

                }

                break;

        }
    }
}
