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

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "detail_encaminhamento" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                       = new THidden("id");
        $paciente_id              = new THidden( "paciente_id" );
        $paciente_nome            = new TEntry( "paciente_nome" );
        //$internamentolocal        = new TCombo("internamentolocal");
        $datainternamento         = new TDate("datainternamento");
        //$remocao                  = new TCombo("remocao");
        $dataremocao              = new TDate("dataremocao");
        $localremocao_id          = new TCombo("localremocao_id");
        //$transferencia            = new TCombo("transferencia");
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
        //$internamentolocal       ->setSize( "38%" );
        $datainternamento        ->setSize( "38%" );
        //$remocao                 ->setSize( "38%" );
        $dataremocao             ->setSize( "38%" );
        $localremocao_id         ->setSize( "38%" );
        //$transferencia           ->setSize( "38%" );
        $datatransferencia       ->setSize( "38%" );
        $localtransferencia_id   ->setSize( "38%" );
        $transportedestino_id    ->setSize( "38%" );
        $especificartransporte   ->setSize( "38%" );
        $datatransporte          ->setSize( "38%" );

        //$internamentolocal       ->setDefaultOption( "..::SELECIONE::.." );
        //$remocao                 ->setDefaultOption( "..::SELECIONE::.." );
        $localremocao_id         ->setDefaultOption( "..::SELECIONE::.." );
        //$transferencia           ->setDefaultOption( "..::SELECIONE::.." );
        $localtransferencia_id   ->setDefaultOption( "..::SELECIONE::.." );
        $transportedestino_id    ->setDefaultOption( "..::SELECIONE::.." );

        //$internamentolocal->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );
        //$remocao          ->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );
        //$transferencia    ->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );

        //$internamentolocal->addItems( [ "S" => "SIM", "N" => "NÃO" ] );
        //$remocao          ->addItems( [ "S" => "SIM", "N" => "NÃO" ] );
        //$transferencia    ->addItems( [ "S" => "SIM", "N" => "NÃO" ] );

        //$internamentolocal->setValue( "N" );
        //$remocao          ->setValue( "N" );
        //$transferencia    ->setValue( "N" );

        $datainternamento   ->setMask( "dd/mm/yyyy" );
        $dataremocao        ->setMask( "dd/mm/yyyy" );
        $datatransferencia  ->setMask( "dd/mm/yyyy" );
        $datatransporte     ->setMask( "dd/mm/yyyy" );

        $datainternamento   ->setDatabaseMask("yyyy-mm-dd");
        $dataremocao        ->setDatabaseMask("yyyy-mm-dd");
        $datatransferencia  ->setDatabaseMask("yyyy-mm-dd");
        $datatransporte     ->setDatabaseMask("yyyy-mm-dd");

        $paciente_nome->setEditable( false );

        $paciente_id->addValidation('Paciente ID', new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Paciente:" ) ], [ $paciente_nome ]);
        switch($_GET['mode']) {
            case 'internamento':
                $this->form->addFields( [ new TLabel( "Data de Internamento: {$redstar}" ) ], [ $datainternamento ] );
                $datainternamento->addValidation('Data de Internamento', new TRequiredValidator );
                break;
            case 'remocao':
                $this->form->addFields( [ new TLabel( "Data de Remoção: {$redstar}") ], [ $dataremocao ] );
                $this->form->addFields( [ new TLabel( "Local de Remoção: {$redstar}") ], [ $localremocao_id ] );
                $dataremocao->addValidation('Data de Remoção', new TRequiredValidator );
                $localremocao_id->addValidation('Local de Remoção', new TRequiredValidator );
                break;
            case 'transferencia':
                $this->form->addFields( [ new TLabel( "Data de Transferência: {$redstar}" ) ], [ $datatransferencia ] );
                $this->form->addFields( [ new TLabel( "Local de Transferência:" ) ], [ $localtransferencia_id ] );
                $this->form->addFields( [ new TLabel( "Destino do Transporte:" ) ], [ $transportedestino_id ] );
                $this->form->addFields( [ new TLabel( "Informações do Transporte:" ) ], [ $especificartransporte ] );
                $this->form->addFields( [ new TLabel( "Data do Transporte:" ) ], [ $datatransporte ] );
                $datatransferencia->addValidation('Data de Transferência', new TRequiredValidator );
                break;
        }

        $this->form->addFields([ $paciente_id, $id ]);

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "did", $did );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar", new TAction( [ "PacientesEncaminhamentoList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onReload( $param = null )
    {
        if (!isset($_GET['mode'])) {
            $action = new TAction( [ "PacientesEncaminhamentoList", "onReload" ] );

            new TMessage( "error", "Algo não deu certo!", $action );
        }
    }

    public function onSave( $param = null )
    {
        $object = $this->form->getData( "BauRecord" );

        try {

            $this->form->validate();

            unset($object->paciente_nome);

            TTransaction::open( "database" );

            $object->situacao = 'ENCAMINHADO';

            $object->store();

            TTransaction::close();

            $action = new TAction( [ "PacientesEncaminhamentoList", "onReload" ] );

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

}
